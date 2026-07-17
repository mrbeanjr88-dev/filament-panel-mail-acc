<?php

namespace App\Listeners;

use App\Events\EmailProcessedEvent;
use App\Models\AppSetting;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookNotificationListener
{
    public function handle(EmailProcessedEvent $event): void
    {
        $webhookUrl = config('email-customs.webhook_url');
        if (! $webhookUrl) {
            return;
        }

        $payload = [
            'event' => $event->action,
            'pending_email_id' => $event->email->id,
            'subject' => $event->email->subject,
            'from' => $event->email->from_email,
            'extracted_amount' => $event->email->extracted_amount,
            'extracted_currency' => $event->email->extracted_currency,
            'bank_account' => $event->email->bankAccount?->label,
            'operator' => $event->operator?->email,
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            $response = Http::timeout(10)
                ->post($webhookUrl, $payload);

            WebhookLog::create([
                'pending_email_id' => $event->email->id,
                'event_type' => $event->action,
                'webhook_url' => $webhookUrl,
                'status_code' => $response->status(),
                'request_payload' => json_encode($payload),
                'response' => $response->body(),
                'success' => $response->successful(),
                'sent_at' => now(),
            ]);

            Log::info('webhook_sent', [
                'pending_email_id' => $event->email->id,
                'action' => $event->action,
                'status_code' => $response->status(),
                'success' => $response->successful(),
            ]);
        } catch (\Throwable $e) {
            WebhookLog::create([
                'pending_email_id' => $event->email->id,
                'event_type' => $event->action,
                'webhook_url' => $webhookUrl,
                'response' => $e->getMessage(),
                'success' => false,
                'sent_at' => now(),
            ]);

            Log::error('webhook_failed', [
                'pending_email_id' => $event->email->id,
                'action' => $event->action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
