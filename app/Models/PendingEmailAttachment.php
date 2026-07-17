<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PendingEmailAttachment extends Model
{
    protected $fillable = [
        'pending_email_id', 'filename', 'mime_type', 'size',
        'disk', 'path', 'content_id', 'is_inline', 'is_removed', 'is_replaced',
    ];

    protected function casts(): array
    {
        return [
            'is_inline'   => 'boolean',
            'is_removed'  => 'boolean',
            'is_replaced' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $attachment) {
            if ($attachment->path && Storage::disk($attachment->disk)->exists($attachment->path)) {
                Storage::disk($attachment->disk)->delete($attachment->path);
            }
        });
    }

    public function pendingEmail(): BelongsTo
    {
        return $this->belongsTo(PendingEmail::class);
    }

    public function contents(): string
    {
        return (string) Storage::disk($this->disk)->get($this->path);
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf'
            || str_ends_with(strtolower($this->filename), '.pdf');
    }
}
