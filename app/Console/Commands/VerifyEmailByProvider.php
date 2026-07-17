<?php

namespace App\Console\Commands;

use App\Models\EmailVerification;
use Illuminate\Console\Command;

class VerifyEmailByProvider extends Command
{
    protected $signature = 'email:verify:by-provider
        {batchId : The batch ID from verify-providers}
        {provider? : Provider to filter by (leave empty to list all)}';

    protected $description = 'List emails by provider for a verification batch';

    public function handle(): int
    {
        $batchId = $this->argument('batchId');
        $provider = $this->argument('provider');

        if (!$provider) {
            // Show all providers
            $summary = EmailVerification::getProviderSummaryDetailed($batchId);
            if (empty($summary)) {
                $this->error("❌ Batch '{$batchId}' not found or empty.");
                return self::FAILURE;
            }

            $this->info("📊 Providers in batch {$batchId}:");
            $this->table(
                ['Provider', 'Label', 'Count', 'Avg Confidence'],
                array_map(fn($s) => [$s['provider'], $s['provider_label'], number_format($s['count']), round($s['avg_confidence'] * 100) . '%'], $summary)
            );
            $this->newLine();
            $this->info("Usage: email:verify:by-provider {$batchId} <provider-name>");
            return self::SUCCESS;
        }

        $query = EmailVerification::where('batch_id', $batchId)
            ->where('provider', $provider);

        $total = $query->count();
        if ($total === 0) {
            $this->error("❌ No emails found for provider '{$provider}' in batch '{$batchId}'.");
            return self::FAILURE;
        }

        $this->info("📧 Emails using provider '{$provider}' in batch {$batchId}: {$total} total");
        $this->newLine();

        // Show first 50
        $records = $query->limit(50)->get();

        $this->table(
            ['Email', 'Domain', 'Confidence', 'Status', 'SMTP'],
            $records->map(fn($r) => [
                $r->email,
                $r->domain,
                round($r->confidence * 100) . '%',
                $r->status,
                $r->smtp_checked ? ($r->smtp_reachable ? '✅' : '❌') : '-',
            ])
        );

        if ($total > 50) {
            $this->info("... and " . ($total - 50) . " more. Export with: email:verify:export {$batchId} --provider={$provider}");
        }

        return self::SUCCESS;
    }
}
