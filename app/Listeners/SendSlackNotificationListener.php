<?php

namespace App\Listeners;

use App\Events\EmailProcessedEvent;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SendSlackNotificationListener
{
    public function handle(EmailProcessedEvent $event): void
    {
        $slackWebhook = config('services.slack.webhook_url');
        if (! $slackWebhook) {
            return;
        }

        $colors = [
            'approved' => '#36a64f',  // green
            'rejected' => '#e74c3c',  // red
            'failed' => '#f39c12',    // orange
        ];

        $emoji = [
            'approved' => '✅',
            'rejected' => '❌',
            'failed' => '⚠️',
        ];

        try {
            $emojiIcon = $emoji[$event->action] ?? '📧';
            $color = $colors[$event->action] ?? '#95a5a6';

            \Illuminate\Support\Facades\Http::post($slackWebhook, [
                'attachments' => [
                    [
                        'color' => $color,
                        'title' => "{$emojiIcon} Email {$event->action}",
                        'title_link' => url("/admin/pending-emails/{$event->email->id}"),
                        'fields' => [
                            [
                                'title' => 'From',
                                'value' => $event->email->from_email,
                                'short' => true,
                            ],
                            [
                                'title' => 'Subject',
                                'value' => $event->email->subject,
                                'short' => false,
                            ],
                            [
                                'title' => 'Amount',
                                'value' => number_format((float) $event->email->extracted_amount, 2) . ' ' . ($event->email->extracted_currency ?? 'N/A'),
                                'short' => true,
                            ],
                            [
                                'title' => 'Bank Account',
                                'value' => $event->email->bankAccount?->label ?? 'N/A',
                                'short' => true,
                            ],
                            [
                                'title' => 'Operator',
                                'value' => $event->operator?->email ?? 'System',
                                'short' => true,
                            ],
                        ],
                        'footer' => 'Email Customs',
                        'ts' => now()->timestamp,
                    ],
                ],
            ]);

            Log::info('slack_notification_sent', [
                'pending_email_id' => $event->email->id,
                'action' => $event->action,
            ]);
        } catch (\Throwable $e) {
            Log::error('slack_notification_failed', [
                'pending_email_id' => $event->email->id,
                'action' => $event->action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
