<?php

namespace App\Models;

use App\Enums\EmailCategory;
use App\Enums\PendingEmailStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;

class PendingEmail extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'email_account_id', 'bank_account_id', 'matched_rule_id',
        'message_id', 'hold_uid',
        'subject', 'from_name', 'from_email', 'to', 'cc', 'date',
        'body_html', 'body_text', 'raw_headers', 'size', 'has_attachments',
        'category', 'tag', 'extracted_amount', 'extracted_currency',
        'extracted_direction', 'invoice_number', 'invoice_date', 'due_date',
        'invoice_issuer', 'vat_amount',
        'status', 'target_folder', 'modified', 'notes', 'last_error',
        'approved_at', 'approved_by', 'rejected_at',
        'pdf_intercepted', 'pdf_modified', 'pdf_pages_original',
        'pdf_pages_modified', 'pdf_modification_summary',
    ];

    protected function casts(): array
    {
        return [
            'to'                => 'array',
            'cc'                => 'array',
            'date'              => 'datetime',
            'has_attachments'  => 'boolean',
            'modified'         => 'boolean',
            'extracted_amount' => 'decimal:2',
            'vat_amount'       => 'decimal:2',
            'invoice_date'     => 'date',
            'due_date'         => 'date',
            'status'           => PendingEmailStatus::class,
            'category'          => EmailCategory::class,
            'approved_at'       => 'datetime',
            'rejected_at'       => 'datetime',
            'pdf_intercepted'   => 'boolean',
            'pdf_modified'      => 'boolean',
        ];
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function matchedRule(): BelongsTo
    {
        return $this->belongsTo(EmailFilterRule::class, 'matched_rule_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PendingEmailAttachment::class);
    }

    public function activeAttachments(): HasMany
    {
        return $this->attachments()->where('is_removed', false);
    }

    public function pdfModifications(): HasMany
    {
        return $this->hasMany(PdfModificationLog::class);
    }

    public function pdfAttachments(): HasMany
    {
        return $this->attachments()->where(function ($q) {
            $q->where('mime_type', 'application/pdf')
                ->orWhere('filename', 'LIKE', '%.pdf');
        });
    }

    /** HTML sanitizat pentru randare în dashboard (vezi AppSetting::sanitize_html). */
    public function safeHtml(): HtmlString
    {
        $html = (string) $this->body_html;

        if (AppSetting::current()->sanitize_html) {
            // Remove dangerous elements and attributes
            $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
            $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);
            $html = preg_replace('#<iframe\b[^>]*>.*?</iframe>#is', '', $html);
            $html = preg_replace('#<object\b[^>]*>.*?</object>#is', '', $html);
            $html = preg_replace('#<embed\b[^>]*>#is', '', $html);

            // Remove event handlers (on* attributes and javascript: protocol)
            $html = preg_replace('#\s+on\w+\s*=\s*["\']?[^"\'>\s]+["\']?#i', '', $html);
            $html = preg_replace('#javascript\s*:#i', '', $html);

            // Remove data attributes that could contain scripts
            $html = preg_replace('#\s+data-[^=]*=["\']?[^"\'>\s]*["\']?#i', '', $html);

            // Use Symfony sanitizer if available for additional safety
            try {
                $sanitizer = \Symfony\Component\HtmlSanitizer\HtmlSanitizer::create();
                $html = $sanitizer->sanitize($html);
            } catch (\Throwable) {
                // Fallback if sanitizer fails
            }
        }

        return new HtmlString($html ?: nl2br(e((string) $this->body_text)));
    }

    public function resolvedTargetFolder(): string
    {
        return $this->target_folder ?: $this->emailAccount->approved_folder;
    }
}
