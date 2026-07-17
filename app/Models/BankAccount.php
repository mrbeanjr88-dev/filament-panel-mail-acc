<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'label', 'bank_name', 'account_holder', 'iban', 'bic', 'account_number',
        'currency', 'email_account_id', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function pendingEmails(): HasMany
    {
        return $this->hasMany(PendingEmail::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->label ?: $this->bank_name) . ' · ' . ($this->iban ?: $this->currency));
    }
}
