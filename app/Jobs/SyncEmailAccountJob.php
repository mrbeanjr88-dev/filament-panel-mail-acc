<?php

namespace App\Jobs;

use App\Models\EmailAccount;
use App\Services\Imap\EmailSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class SyncEmailAccountJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 300;

    public function __construct(public EmailAccount $account)
    {
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('imap-sync-' . $this->account->id))->expireAfter(360)];
    }

    public function handle(EmailSyncService $sync): void
    {
        $sync->sync($this->account);
    }
}
