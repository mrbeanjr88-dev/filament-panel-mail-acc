<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EmailVerification extends Model
{
    protected $fillable = [
        'batch_id', 'email', 'domain',
        'provider', 'provider_label', 'confidence',
        'imap_host', 'smtp_host', 'port',
        'mx_records',
        'smtp_checked', 'smtp_reachable', 'smtp_response_code', 'smtp_error', 'smtp_mx_host',
        'status', 'error', 'verification_time_ms',
        'source', 'raw_line',
    ];

    protected function casts(): array
    {
        return [
            'mx_records'         => 'array',
            'confidence'         => 'float',
            'smtp_checked'       => 'boolean',
            'smtp_reachable'     => 'boolean',
            'smtp_response_code' => 'integer',
            'verification_time_ms' => 'integer',
        ];
    }

    // --- Scopes ---

    public function scopeForBatch(Builder $query, string $batchId): Builder
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeForProvider(Builder $query, string $provider): Builder
    {
        return $query->where('provider', $provider);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeReachable(Builder $query): Builder
    {
        return $query->where('smtp_reachable', true);
    }

    public function scopeUnknown(Builder $query): Builder
    {
        return $query->where('provider', 'unknown');
    }

    // --- Helpers ---

    /**
     * Get provider distribution for a batch.
     * Returns array of [provider => count].
     */
    public static function getProviderSummary(string $batchId): array
    {
        return static::forBatch($batchId)
            ->selectRaw('provider, provider_label, COUNT(*) as count')
            ->groupBy('provider', 'provider_label')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'provider')
            ->toArray();
    }

    /**
     * Get full provider details for a batch.
     */
    public static function getProviderSummaryDetailed(string $batchId): array
    {
        return static::forBatch($batchId)
            ->selectRaw('provider, provider_label, COUNT(*) as count, AVG(confidence) as avg_confidence')
            ->groupBy('provider', 'provider_label')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }

    /**
     * Get domain distribution for a batch.
     */
    public static function getDomainSummary(string $batchId): array
    {
        return static::forBatch($batchId)
            ->selectRaw('domain, provider, COUNT(*) as count')
            ->groupBy('domain', 'provider')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }

    /**
     * Get SMTP reachability stats for a batch.
     */
    public static function getSmtpSummary(string $batchId): array
    {
        return static::forBatch($batchId)
            ->where('smtp_checked', true)
            ->selectRaw('
                COUNT(*) as total_checked,
                SUM(CASE WHEN smtp_reachable = 1 THEN 1 ELSE 0 END) as reachable,
                SUM(CASE WHEN smtp_reachable = 0 THEN 1 ELSE 0 END) as unreachable
            ')
            ->first()
            ->toArray();
    }

    /**
     * Get batch statistics overview.
     */
    public static function getBatchStats(string $batchId): array
    {
        $total = static::forBatch($batchId)->count();
        $verified = static::forBatch($batchId)->verified()->count();
        $failed = static::forBatch($batchId)->failed()->count();
        $pending = static::forBatch($batchId)->pending()->count();
        $unknown = static::forBatch($batchId)->unknown()->count();

        return [
            'total'    => $total,
            'verified' => $verified,
            'failed'   => $failed,
            'pending'  => $pending,
            'unknown'  => $unknown,
            'providers' => static::getProviderSummary($batchId),
        ];
    }
}
