<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
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

    protected function clientFor(User $user): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessType('offline');
        $client->setScopes([GoogleCalendar::CALENDAR, GoogleCalendar::CALENDAR_EVENTS]);

        $expiresIn = $user->google_token_expires_at
            ? max(1, now()->diffInSeconds($user->google_token_expires_at, false))
            : 1;

        // Token atual (created no passado p/ o client conseguir calcular expiração)
        $client->setAccessToken([
            'access_token'  => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in'    => $expiresIn,
            'created'       => now()->subHour()->timestamp,
        ]);

        // Refresh se precisar
        if ($client->isAccessTokenExpired()) {
            if (!$user->google_refresh_token) {
                throw new \RuntimeException('Token do Google expirado e não há refresh_token salvo.');
            }

            $new = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

            if (isset($new['error'])) {
                throw new \RuntimeException('Falha ao atualizar o token Google: '.$new['error']);
            }

            $user->update([
                'google_access_token'     => $new['access_token'] ?? null,
                'google_token_expires_at' => now()->addSeconds($new['expires_in'] ?? 3500),
            ]);

            $client->setAccessToken(array_merge($client->getAccessToken() ?: [], $new));
        }

        return $client;
    }

    /**
     * payload:
     *  - summary (string) [obrigatório]
     *  - description (string|null)
     *  - start (Carbon|string|DateTimeInterface)
     *  - end   (Carbon|string|DateTimeInterface)
     *  - attendees (array[['email'=>...], ...]) [opcional]
     *  - reminders (array) [opcional] mesmo formato do Calendar API
     *  - conference (bool) [opcional] default true -> tenta criar Meet e, se 403, cria sem Meet
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
        ]);

        $event->setStart($this->asEventDateTime($payload['start'], $tz));
        $event->setEnd($this->asEventDateTime($payload['end'], $tz));

        if (isset($payload['reminders'])) {
            $event->setReminders($payload['reminders']);
        } else {
            // defaultzinho útil
            $event->setReminders([
                'useDefault' => false,
                'overrides'  => [
                    ['method' => 'popup', 'minutes' => 1440],
                    ['method' => 'popup', 'minutes' => 60],
                ],
            ]);
        }

        $wantConference = array_key_exists('conference', $payload) ? (bool)$payload['conference'] : true;

        // Tenta com Meet; se der 403, cria sem Meet
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
                ['conferenceDataVersion' => $wantConference ? 1 : 0]
            );
        } catch (GoogleServiceException $e) {
            // Muitos tenants não têm permissão para criar Meet -> 403
            if ($wantConference && $e->getCode() === 403) {
                unset($event['conferenceData']);
                $created = $service->events->insert($this->calendarId($user), $event);
            } else {
                throw $e;
            }
        }

        return $created->id;
    }

    public function updateEvent(User $user, string $eventId, array $payload): void
    {
        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);

        $event = $service->events->get($this->calendarId($user), $eventId);

        $tz = config('app.timezone', 'America/Sao_Paulo');

        $event->setSummary($payload['summary']);
        $event->setDescription($payload['description'] ?? null);
        $event->setStart($this->asEventDateTime($payload['start'], $tz));
        $event->setEnd($this->asEventDateTime($payload['end'], $tz));

        if (isset($payload['attendees'])) {
            $event->setAttendees($payload['attendees']);
        }
        if (isset($payload['reminders'])) {
            $event->setReminders($payload['reminders']);
        }

        // Não tenta alterar conference aqui (mantém o que já existe)
        $service->events->update($this->calendarId($user), $eventId, $event);
    }

    public function deleteEvent(User $user, ?string $eventId): void
    {
        if (!$eventId) return;

        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);

        // Se já tiver sido removido manualmente no Google, ignore 410/404
        try {
            $service->events->delete($this->calendarId($user), $eventId);
        } catch (GoogleServiceException $e) {
            if (!in_array($e->getCode(), [404, 410], true)) {
                throw $e;
            }
        }
    }
}
