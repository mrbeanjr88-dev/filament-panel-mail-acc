<?php

namespace App\Console\Commands;

use App\Models\EmailVerification;
use Illuminate\Console\Command;

class VerifyEmailStats extends Command
{
    protected $signature = 'email:verify:stats {batchId : The batch ID from verify-providers}';

    protected $description = 'Show provider statistics for an email verification batch';

    public function handle(): int
    {
        $batchId = $this->argument('batchId');

        $exists = EmailVerification::where('batch_id', $batchId)->exists();
        if (!$exists) {
            $this->error("❌ Batch '{$batchId}' not found.");
            return self::FAILURE;
        }

        $stats = EmailVerification::getBatchStats($batchId);
        $detailedProviders = EmailVerification::getProviderSummaryDetailed($batchId);
        $smtpStats = EmailVerification::getSmtpSummary($batchId);

        $this->info("📊 Verification Stats for Batch: {$batchId}");
        $this->newLine();

        // Overview
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Emails', $stats['total']],
                ['Verified', $stats['verified']],
                ['Failed', $stats['failed']],
                ['Pending', $stats['pending']],
                ['Unknown Provider', $stats['unknown']],
            ]
        );

        // Provider breakdown
        $this->info("📧 Provider Distribution:");
        $providerRows = [];
        foreach ($detailedProviders as $p) {
            $percentage = $stats['total'] > 0 ? round(($p['count'] / $stats['total']) * 100, 1) : 0;
            $providerRows[] = [
                $p['provider'],
                $p['provider_label'],
                number_format($p['count']),
                "{$percentage}%",
                round($p['avg_confidence'] * 100) . '%',
            ];
        }
        $this->table(
            ['Provider', 'Label', 'Count', 'Percentage', 'Avg Confidence'],
            $providerRows
        );

        // SMTP stats
        if ($smtpStats && $smtpStats['total_checked'] > 0) {
            $this->info("📬 SMTP Verification:");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Checked', $smtpStats['total_checked']],
                    ['Reachable', $smtpStats['reachable']],
                    ['Unreachable', $smtpStats['unreachable']],
                ]
            );
        }

        // Top 20 domains
        $this->info("🌐 Top 20 Domains:");
        $domainSummary = EmailVerification::getDomainSummary($batchId);
        $domainRows = array_slice($domainSummary, 0, 20);
        $this->table(
            ['Domain', 'Provider', 'Count'],
            array_map(fn($d) => [$d['domain'], $d['provider'], number_format($d['count'])], $domainRows)
        );

        return self::SUCCESS;
    }
}
