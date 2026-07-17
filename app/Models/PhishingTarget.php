<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhishingTarget extends Model
{
    protected $fillable = [
        'campaign_id', 'email', 'domain', 'provider',
        'user_display_name', 'status', 'tracking_token',
        'raw_line', 'sent_at', 'opened_at', 'clicked_at', 'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at'     => 'datetime',
            'opened_at'   => 'datetime',
            'clicked_at'  => 'datetime',
            'captured_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(PhishingCampaign::class, 'campaign_id');
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
