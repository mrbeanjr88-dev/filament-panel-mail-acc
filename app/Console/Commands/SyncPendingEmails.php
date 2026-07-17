<?php

namespace App\Console\Commands;

use App\Jobs\SyncEmailAccountJob;
use App\Models\EmailAccount;
use App\Services\Imap\EmailSyncService;
use Illuminate\Console\Command;

class SyncPendingEmails extends Command
{
    protected $signature = 'emails:sync
        {account? : A single account ID (default: all active accounts)}
        {--queue : Dispatch to queue instead of running synchronously}';

    protected $description = 'Fetch emails from IMAP accounts into quarantine (Hold) and apply filters';

    public function handle(EmailSyncService $sync): int
    {
        $accounts = EmailAccount::query()
            ->where('is_active', true)
            ->when($this->argument('account'), fn ($q, $id) => $q->whereKey($id))
            ->when(! $this->argument('account'), fn ($q) => $q->where('auto_sync', true))
            ->get();

        if ($accounts->isEmpty()) {
            $this->warn('No accounts to sync.');

            return self::SUCCESS;
        }

        foreach ($accounts as $account) {
            if ($this->option('queue')) {
                SyncEmailAccountJob::dispatch($account);
                $this->line("⏳ Dispatched: {$account->name}");

                continue;
            }

            try {
                $count = $sync->sync($account);
                $this->info("✅ {$account->name}: {$count} new emails quarantined.");
            } catch (\Throwable $e) {
                $this->error("❌ {$account->name}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
