<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = [
        'pending_email_id',
        'event_type',
        'webhook_url',
        'status_code',
        'request_payload',
        'response',
        'success',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function pendingEmail(): BelongsTo
    {
        return $this->belongsTo(PendingEmail::class);
    }
}
