<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Setări globale (rând singleton, id=1). Folosit ca AppSetting::current().
 */
class AppSetting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'capture_mode', 'mark_as_read', 'auto_apply_rules',
        'extract_bank_data', 'sanitize_html', 'attachments_disk',
        'default_currency', 'default_hold_folder', 'default_fetch_limit',
        'intercept_pdf_attachments', 'auto_apply_pdf_templates',
        'pdf_watermark_text', 'pdf_stamp_text', 'pdf_stamp_operator',
        'pdf_output_folder',
    ];

    protected function casts(): array
    {
        return [
            'mark_as_read'              => 'boolean',
            'auto_apply_rules'          => 'boolean',
            'extract_bank_data'         => 'boolean',
            'sanitize_html'             => 'boolean',
            'intercept_pdf_attachments' => 'boolean',
            'auto_apply_pdf_templates'  => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOr(fn () => static::query()->create([]));
    }
}
