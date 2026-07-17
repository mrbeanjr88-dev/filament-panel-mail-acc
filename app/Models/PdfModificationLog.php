<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfModificationLog extends Model
{
    protected $fillable = [
        'pending_email_id', 'pending_email_attachment_id', 'pdf_template_id',
        'operation', 'params', 'result', 'status', 'error', 'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'params' => 'array',
            'result' => 'array',
        ];
    }

    public function pendingEmail(): BelongsTo
    {
        return $this->belongsTo(PendingEmail::class);
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(PendingEmailAttachment::class, 'pending_email_attachment_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PdfTemplate::class, 'pdf_template_id');
    }
}
