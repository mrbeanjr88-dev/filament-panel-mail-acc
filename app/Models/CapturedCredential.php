<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapturedCredential extends Model
{
    protected $fillable = [
        'campaign_id', 'target_id', 'email', 'password',
        'provider', 'ip_address', 'user_agent', 'country',
        'city', 'extra_data', 'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'extra_data'  => 'array',
            'captured_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(PhishingCampaign::class, 'campaign_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(PhishingTarget::class, 'target_id');
    }
}
