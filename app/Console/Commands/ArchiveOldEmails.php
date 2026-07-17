<?php

namespace App\Console\Commands;

use App\Models\PendingEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ArchiveOldEmails extends Command
{
    protected $signature = 'email:archive {--days=90 : Archive emails older than N days}';
    protected $description = 'Archive (soft delete) old processed emails for data management';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Archiving emails older than {$days} days...");

        try {
            // Soft delete old processed emails
            $archivedCount = PendingEmail::where('status', 'processed')
                ->where('approved_at', '<', now()->subDays($days))
                ->delete();

            $this->info("✓ Archived {$archivedCount} processed emails");

            // Also soft delete rejected emails older than 30 days
            $rejectedCount = PendingEmail::where('status', 'rejected')
                ->where('rejected_at', '<', now()->subDays(30))
                ->delete();

            $this->info("✓ Archived {$rejectedCount} rejected emails");

            // Permanently delete very old archived emails (>1 year)
            $permanentlyDeleted = PendingEmail::onlyTrashed()
                ->where('deleted_at', '<', now()->subYear())
                ->forceDelete();

            $this->info("✓ Permanently deleted {$permanentlyDeleted} very old archived emails");

            $total = $archivedCount + $rejectedCount;

            Log::info('email_archive_completed', [
                'archived_count' => $total,
                'permanently_deleted' => $permanentlyDeleted,
                'days_threshold' => $days,
            ]);

            $this->info("✅ Email archival completed successfully");
            return 0;
        } catch (\Throwable $e) {
            $this->error("❌ Error during archival: {$e->getMessage()}");
            Log::error('email_archive_failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }
}
