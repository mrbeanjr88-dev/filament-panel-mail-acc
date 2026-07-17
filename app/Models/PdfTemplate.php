<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PdfTemplate extends Model
{
    protected $fillable = [
        'name', 'description', 'type', 'is_active',
        'config', 'email_account_id', 'auto_apply', 'priority',
        'match_criteria',
    ];

    protected function casts(): array
    {
        return [
            'is_active'      => 'boolean',
            'auto_apply'     => 'boolean',
            'config'         => 'array',
            'match_criteria' => 'array',
            'priority'       => 'integer',
        ];
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function modificationLogs(): HasMany
    {
        return $this->hasMany(PdfModificationLog::class);
    }

    /**
     * Verifică dacă template-ul se potrivește cu un email dat.
     */
    public function matches(PendingEmail $email): bool
    {
        $criteria = $this->match_criteria ?? [];

        if (empty($criteria)) {
            return true; // fără criterii = potrivește tot
        }

        if (! empty($criteria['from_contains'])) {
            $from = strtolower(trim(($email->from_name ?? '') . ' ' . ($email->from_email ?? '')));
            if (! str_contains($from, strtolower($criteria['from_contains']))) {
                return false;
            }
        }

        if (! empty($criteria['subject_contains'])) {
            $subject = strtolower((string) $email->subject);
            if (! str_contains($subject, strtolower($criteria['subject_contains']))) {
                return false;
            }
        }

        if (! empty($criteria['body_contains'])) {
            $body = strtolower((string) ($email->body_text ?: strip_tags((string) $email->body_html)));
            if (! str_contains($body, strtolower($criteria['body_contains']))) {
                return false;
            }
        }

        if (! empty($criteria['require_pdf'])) {
            $hasPdf = $email->attachments()->where(function ($q) {
                $q->where('mime_type', 'application/pdf')
                    ->orWhere('filename', 'LIKE', '%.pdf');
            })->exists();
            if (! $hasPdf) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returnează config-ul ca parametrii pentru PdfModifierService.
     */
    public function toOperationParams(): array
    {
        return [
            'type'   => $this->type,
            'params' => $this->config ?? [],
        ];
    }
}
