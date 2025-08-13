<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventReminders;
use Google\Service\Calendar\EventReminder;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Support\Carbon;
use App\Models\User;

class GoogleCalendarService
{
    protected function calendarId(User $user): string
    {
        return $user->google_calendar_id ?: 'primary';
    }

    protected function asEventDateTime($when, string $tz): EventDateTime
    {
        $dt = Carbon::parse($when)->toRfc3339String();
        $obj = new EventDateTime();
        $obj->setDateTime($dt);
        $obj->setTimeZone($tz);
        return $obj;
    }

    protected function buildReminders(array $rem = null): ?EventReminders
    {
        $rem = $rem ?? [
            'useDefault' => false,
            'overrides'  => [
                ['method' => 'popup', 'minutes' => 1440],
                ['method' => 'popup', 'minutes' => 60],
            ],
        ];

        $reminders = new EventReminders();
        $reminders->setUseDefault((bool)($rem['useDefault'] ?? false));

        $overridesObjs = [];
        if (!empty($rem['overrides']) && is_array($rem['overrides'])) {
            foreach ($rem['overrides'] as $o) {
                $r = new EventReminder();
                if (isset($o['method']))  { $r->setMethod($o['method']); }
                if (isset($o['minutes'])) { $r->setMinutes((int)$o['minutes']); }
                $overridesObjs[] = $r;
            }
        }
        $reminders->setOverrides($overridesObjs);

        return $reminders;
    }

    /** Marca o usuário como desconectado do Google e limpa tokens. */
    protected function disconnectUser(User $user): void
    {
        $user->forceFill([
            'google_access_token'     => null,
            'google_refresh_token'    => null,
            'google_token_expires_at' => null,
            'google_connected'        => false,
        ])->save();
    }

    protected function clientFor(User $user): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessType('offline');
        $client->setScopes([GoogleCalendar::CALENDAR, GoogleCalendar::CALENDAR_EVENTS]);

        // Access token atual (pode estar expirado)
        $expiresIn = $user->google_token_expires_at
            ? max(1, now()->diffInSeconds($user->google_token_expires_at, false))
            : 1;

        $client->setAccessToken([
            'access_token'  => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in'    => $expiresIn,
            'created'       => now()->subHour()->timestamp,
        ]);

        // Se expirou, tenta refrescar; se falhar, desconecta e orienta reconexão
        if ($client->isAccessTokenExpired()) {
            if (!$user->google_refresh_token) {
                $this->disconnectUser($user);
                throw new \RuntimeException('Sua conexão com o Google expirou. Reconecte sua conta do Google.');
            }

            try {
                $new = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            } catch (\Throwable $e) {
                $this->disconnectUser($user);
                throw new \RuntimeException('Não foi possível renovar o acesso ao Google. Conecte novamente sua conta.');
            }

            // Erro explícito do Google ou ausência de access_token → invalida conexão
            if (isset($new['error']) || empty($new['access_token'])) {
                // Opcional: \Log::warning('Google token refresh falhou', ['user_id'=>$user->id,'error'=>$new['error'] ?? 'no_access_token']);
                $this->disconnectUser($user);
                throw new \RuntimeException('Tokens do Google inválidos/revogados. Reconecte sua conta do Google.');
            }

            // Alguns fluxos devolvem refresh_token novo
            if (!empty($new['refresh_token'])) {
                $user->google_refresh_token = $new['refresh_token'];
            }

            $user->google_access_token     = $new['access_token'];
            $user->google_token_expires_at = now()->addSeconds($new['expires_in'] ?? 3500);
            $user->google_connected        = true; // segue conectado
            $user->save();

            $client->setAccessToken(array_merge($client->getAccessToken() ?: [], $new));
        }

        return $client;
    }

    /**
     * Cria um evento no Google Calendar.
     *
     * payload:
     *  - id (string) [opcional] -> ID determinístico
     *  - summary (string) [obrigatório]
     *  - description (string|null)
     *  - start (Carbon|string|DateTimeInterface)
     *  - end   (Carbon|string|DateTimeInterface)
     *  - attendees (array[['email'=>...], ...]) [opcional]
     *  - reminders (array) [opcional]
     *  - conference (bool) [opcional] default true (tenta criar Meet)
     *  - location (string) [opcional]
     */
    public function createEvent(User $user, array $payload): string
    {
        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);
        $tz = config('app.timezone', 'America/Sao_Paulo');

        $event = new Event([
            'summary'     => $payload['summary'],
            'description' => $payload['description'] ?? null,
            'attendees'   => $payload['attendees'] ?? [],
            'location'    => $payload['location']   ?? null,
        ]);

        if (!empty($payload['id'])) {
            $event->setId(strtolower($payload['id']));
        }

        $event->setStart($this->asEventDateTime($payload['start'], $tz));
        $event->setEnd($this->asEventDateTime($payload['end'], $tz));
        $event->setReminders($this->buildReminders($payload['reminders'] ?? null));

        $wantConference = array_key_exists('conference', $payload) ? (bool)$payload['conference'] : true;

        if ($wantConference) {
            $event['conferenceData'] = [
                'createRequest' => [
                    'requestId' => 'psigestor-' . uniqid('', true),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ];
        }

        try {
            $created = $service->events->insert(
                $this->calendarId($user),
                $event,
                ['conferenceDataVersion' => 1]
            );
        } catch (GoogleServiceException $e) {
            if ($wantConference && $e->getCode() === 403) {
                unset($event['conferenceData']);
                try {
                    $created = $service->events->insert($this->calendarId($user), $event);
                } catch (GoogleServiceException $e2) {
                    if ($e2->getCode() === 400 && !empty($event['attendees'])) {
                        unset($event['attendees']);
                        $created = $service->events->insert($this->calendarId($user), $event);
                    } else {
                        throw $e2;
                    }
                }
            } elseif ($e->getCode() === 400 && !empty($event['attendees'])) {
                unset($event['attendees']);
                $created = $service->events->insert($this->calendarId($user), $event);
            } else {
                throw $e;
            }
        }

        return $created->id;
    }

    /** Atualiza um evento existente. */
    public function updateEvent(User $user, string $eventId, array $payload): void
    {
        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);

        // 👇 traga o evento já com conferenceData
        $event = $service->events->get(
            $this->calendarId($user),
            $eventId,
            ['conferenceDataVersion' => 1]
        );

        $tz = config('app.timezone', 'America/Sao_Paulo');

        $event->setSummary($payload['summary']);
        $event->setDescription($payload['description'] ?? null);

        if (array_key_exists('location', $payload)) {
            $event->setLocation($payload['location']);
        }

        $event->setStart($this->asEventDateTime($payload['start'], $tz));
        $event->setEnd($this->asEventDateTime($payload['end'], $tz));

        if (array_key_exists('attendees', $payload)) {
            $event->setAttendees($payload['attendees']);
        }
        if (array_key_exists('reminders', $payload)) {
            $event->setReminders($this->buildReminders($payload['reminders']));
        }

        // 👇 se ainda não existe Meet, cria agora
        $hasConference = $event->getConferenceData()
            && $event->getConferenceData()->getEntryPoints();

        if (!$hasConference) {
            $event['conferenceData'] = [
                'createRequest' => [
                    'requestId' => 'psigestor-' . uniqid('', true),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ];
        }

        try {
            // 👇 atualize sempre com conferenceDataVersion=1
            $service->events->update(
                $this->calendarId($user),
                $eventId,
                $event,
                ['conferenceDataVersion' => 1]
            );
        } catch (GoogleServiceException $e) {
            if ($e->getCode() === 400 && !empty($event['attendees'])) {
                unset($event['attendees']);
                $service->events->update(
                    $this->calendarId($user),
                    $eventId,
                    $event,
                    ['conferenceDataVersion' => 1]
                );
            } else {
                throw $e;
            }
        }
    }


    /** Remove um evento (idempotente). */
    public function deleteEvent(User $user, ?string $eventId): void
    {
        if (!$eventId) return;

        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);

        try {
            $service->events->delete($this->calendarId($user), $eventId);
        } catch (GoogleServiceException $e) {
            if (!in_array($e->getCode(), [404, 410], true)) {
                throw $e;
            }
        }
    }

    /** Busca um evento com conferenceData (para extrair link do Meet). */
    public function getEvent(User $user, string $eventId): Event
    {
        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);

        return $service->events->get(
            $this->calendarId($user),
            $eventId,
            ['conferenceDataVersion' => 1]
        );
    }

    /** Extrai o link do Google Meet de um Event (quando existir). */
    public function extractMeetUrl(Event $event): ?string
    {
        if ($event->getHangoutLink()) {
            return $event->getHangoutLink();
        }
        $conf = $event->getConferenceData();
        if ($conf && $conf->getEntryPoints()) {
            foreach ($conf->getEntryPoints() as $ep) {
                if ($ep->getEntryPointType() === 'video' && $ep->getUri()) {
                    return $ep->getUri();
                }
            }
        }
        return null;
    }
}
