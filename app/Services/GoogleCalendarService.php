<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Illuminate\Support\Carbon;
use App\Models\User;

class GoogleCalendarService
{
    protected function clientFor(User $user): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessType('offline');
        $client->setScopes([GoogleCalendar::CALENDAR, GoogleCalendar::CALENDAR_EVENTS]);

        // monta token atual
        $client->setAccessToken([
            'access_token'  => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in'    => max(1, now()->diffInSeconds($user->google_token_expires_at, false)),
            'created'       => now()->subHour()->timestamp,
        ]);

        // refresh se precisar
        if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
            $new = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $user->update([
                'google_access_token'     => $new['access_token'] ?? null,
                'google_token_expires_at' => now()->addSeconds($new['expires_in'] ?? 3500),
            ]);
            $client->setAccessToken($new);
        }

        return $client;
    }

    public function createEvent(User $user, array $payload): string
    {
        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);

        $event = new GoogleCalendar\Event([
            'summary'     => $payload['summary'],
            'description' => $payload['description'] ?? null,
            'start'       => [
                'dateTime' => Carbon::parse($payload['start'])->toRfc3339String(),
                'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
            ],
            'end'         => [
                'dateTime' => Carbon::parse($payload['end'])->toRfc3339String(),
                'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
            ],
            'attendees'   => $payload['attendees'] ?? [],
            'reminders'   => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 1440],
                    ['method' => 'popup', 'minutes' => 60],
                ],
            ],
            // Meet opcional
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => 'psigestor-'.uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ],
            ],
        ]);

        $created = $service->events->insert(
            $user->google_calendar_id ?: 'primary',
            $event,
            ['conferenceDataVersion' => 1]
        );

        return $created->id;
    }

    public function updateEvent(User $user, string $eventId, array $payload): void
    {
        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);

        $event = $service->events->get($user->google_calendar_id ?: 'primary', $eventId);
        $event->setSummary($payload['summary']);
        $event->setDescription($payload['description'] ?? null);
        $event->setStart([
            'dateTime' => Carbon::parse($payload['start'])->toRfc3339String(),
            'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
        ]);
        $event->setEnd([
            'dateTime' => Carbon::parse($payload['end'])->toRfc3339String(),
            'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
        ]);

        $service->events->update($user->google_calendar_id ?: 'primary', $eventId, $event);
    }

    public function deleteEvent(User $user, ?string $eventId): void
    {
        if (!$eventId) return;
        $client  = $this->clientFor($user);
        $service = new GoogleCalendar($client);
        $service->events->delete($user->google_calendar_id ?: 'primary', $eventId);
    }
}
