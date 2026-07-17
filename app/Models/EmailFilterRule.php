<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailFilterRule extends Model
{
    use Auditable;
    protected $fillable = [
        'name', 'is_active', 'priority', 'match_type', 'email_account_id',
        'from_contains', 'from_regex', 'subject_contains', 'subject_regex',
        'to_contains', 'body_contains', 'require_attachment', 'amount_min', 'amount_max',
        'then_bank_account_id', 'then_category', 'then_tag', 'then_target_folder',
        'then_auto_approve', 'then_reject', 'stop_processing',
    ];

    protected function casts(): array
    {
        return [
            'is_active'          => 'boolean',
            'priority'           => 'integer',
            'require_attachment' => 'boolean',
            'amount_min'         => 'decimal:2',
            'amount_max'         => 'decimal:2',
            'then_auto_approve'  => 'boolean',
            'then_reject'        => 'boolean',
            'stop_processing'    => 'boolean',
        ];
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'then_bank_account_id');
    }

    public function matchedEmails(): HasMany
    {
        return $this->hasMany(PendingEmail::class, 'matched_rule_id');
    }
}
