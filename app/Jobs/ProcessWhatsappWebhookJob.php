<?php

namespace App\Jobs;

use App\Models\WebhookInbox;
use App\Services\WhatsappWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessWhatsappWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 6;
    public int $timeout = 25;
    public array $backoff = [5, 15, 45, 120, 300, 600];

    public function __construct(public int $inboxId) {}

    public function handle(WhatsappWebhookService $service): void
    {
        $inbox = WebhookInbox::find($this->inboxId);

        if (!$inbox) {
            Log::warning('[WPP JOB] Inbox não encontrado', ['inbox_id' => $this->inboxId]);
            return;
        }

        if ($inbox->status === 'PROCESSED') return;

        $inbox->update([
            'status' => 'PROCESSING',
            'attempts' => $inbox->attempts + 1,
        ]);

        try {
            $payload = json_decode($inbox->payload_json, true) ?: [];
            $service->processar($payload, $inbox->request_id);

            $inbox->update([
                'status' => 'PROCESSED',
                'last_error' => null,
            ]);
        } catch (Throwable $e) {
            $inbox->update([
                'status' => 'FAILED',
                'last_error' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error('[WPP JOB] Falha no processamento', [
                'inbox_id' => $inbox->id,
                'request_id' => $inbox->request_id,
                'error' => $e->getMessage(),
            ]);

            throw $e; // garante retry/backoff
        }
    }
}
