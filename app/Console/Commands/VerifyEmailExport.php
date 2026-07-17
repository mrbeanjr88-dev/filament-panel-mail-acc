<?php

namespace App\Console\Commands;

use App\Models\EmailVerification;
use Illuminate\Console\Command;

class VerifyEmailExport extends Command
{
    protected $signature = 'email:verify:export
        {batchId : The batch ID from verify-providers}
        {--provider= : Filter by specific provider (optional)}
        {--output= : Output file path (default: auto-generated)}';

    protected $description = 'Export email verification results to CSV';

    public function handle(): int
    {
        $batchId = $this->argument('batchId');
        $provider = $this->option('provider');
        $outputPath = $this->option('output');

        $query = EmailVerification::where('batch_id', $batchId);

        if ($provider) {
            $query->where('provider', $provider);
        }

        $count = $query->count();
        if ($count === 0) {
            $this->error("❌ No results found for batch '{$batchId}'" . ($provider ? " with provider '{$provider}'" : ""));
            return self::FAILURE;
        }

        if (!$outputPath) {
            $outputPath = "email_verification_{$batchId}" . ($provider ? "_{$provider}" : "") . ".csv";
        }

        $this->info("📤 Exporting {$count} records to: {$outputPath}");

        $handle = fopen($outputPath, 'w');
        if (!$handle) {
            $this->error("❌ Cannot create output file: {$outputPath}");
            return self::FAILURE;
        }

        // Header
        fputcsv($handle, [
            'email', 'domain', 'provider', 'provider_label', 'confidence',
            'imap_host', 'smtp_host', 'port',
            'mx_primary', 'mx_all',
            'smtp_checked', 'smtp_reachable', 'smtp_response_code', 'smtp_error',
            'status', 'verification_time_ms', 'source',
        ]);

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(1000, function ($records) use ($handle, $bar) {
            foreach ($records as $record) {
                $mxPrimary = $record->mx_records[0]['host'] ?? '';
                $mxAll = implode(' | ', array_column($record->mx_records ?? [], 'host'));

                fputcsv($handle, [
                    $record->email,
                    $record->domain,
                    $record->provider,
                    $record->provider_label,
                    $record->confidence,
                    $record->imap_host ?? '',
                    $record->smtp_host ?? '',
                    $record->port ?? '',
                    $mxPrimary,
                    $mxAll,
                    $record->smtp_checked ? 'yes' : 'no',
                    $record->smtp_reachable === null ? 'n/a' : ($record->smtp_reachable ? 'yes' : 'no'),
                    $record->smtp_response_code ?? '',
                    $record->smtp_error ?? '',
                    $record->status,
                    $record->verification_time_ms ?? '',
                    $record->source ?? '',
                ]);
                $bar->advance();
            }
        });

        $bar->finish();
        fclose($handle);

        $this->newLine(2);
        $this->info("✅ Exported {$count} records to: {$outputPath}");

        // Show summary of what was exported
        $summary = EmailVerification::getProviderSummary($batchId);
        if ($provider) {
            $summary = [$provider => $summary[$provider] ?? 0];
        }
        foreach ($summary as $p => $c) {
            $this->info("   {$p}: {$c}");
        }

        return self::SUCCESS;
    }
}
