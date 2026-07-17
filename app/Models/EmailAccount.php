<?php

namespace App\Models;

use App\Services\Imap\ImapClientFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webklex\PHPIMAP\Client;

class EmailAccount extends Model
{
    protected $fillable = [
        'name', 'email',
        'host', 'port', 'encryption', 'validate_cert', 'protocol',
        'username', 'password', 'authentication',
        'inbox_folder', 'hold_folder', 'approved_folder', 'rejected_folder',
        'is_active', 'auto_sync', 'fetch_limit', 'last_synced_at', 'last_error',
    ];

    protected function casts(): array
    {
        return [
            'password'       => 'encrypted',
            'validate_cert'  => 'boolean',
            'is_active'      => 'boolean',
            'auto_sync'      => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    protected $hidden = ['password'];

    public function pendingEmails(): HasMany
    {
        return $this->hasMany(PendingEmail::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function makeClient(): Client
    {
        return app(ImapClientFactory::class)->for($this);
    }

    public function effectiveRejectedFolder(): string
    {
        return $this->rejected_folder ?: 'INBOX.Trash';
    }
}
