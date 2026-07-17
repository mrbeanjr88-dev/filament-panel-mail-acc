<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhishingCampaign extends Model
{
    protected $fillable = [
        'name', 'provider', 'campaign_type', 'subject', 'body_html', 'body_text',
        'from_name', 'from_email', 'reply_to', 'tracking_id',
        'status', 'total_sent', 'total_opened', 'total_clicked', 'total_captured',
        'target_domains', 'started_at', 'completed_at',
        'evilginx_domain', 'evilginx_phishlet', 'deep_link_mode', 'auto_connect_enabled',
    ];

    protected function casts(): array
    {
        return [
            'target_domains'        => 'array',
            'started_at'            => 'datetime',
            'completed_at'          => 'datetime',
            'auto_connect_enabled'  => 'boolean',
        ];
    }

    public function targets(): HasMany
    {
        return $this->hasMany(PhishingTarget::class, 'campaign_id');
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(CapturedCredential::class, 'campaign_id');
    }

    public static function generateTrackingId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
