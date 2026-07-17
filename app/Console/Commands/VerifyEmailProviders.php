<?php

namespace App\Console\Commands;

use App\Jobs\VerifyEmailProvidersJob;
use App\Models\EmailVerification;
use App\Services\EmailProviderVerifier;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class VerifyEmailProviders extends Command
{
    protected $signature = 'email:verify-providers
        {source : Path to CSV/TXT file with email addresses}
        {--column=email : CSV column name containing emails (default: email)}
        {--delimiter=, : CSV delimiter}
        {--smtp : Perform SMTP verification (slower, ~2s per email)}
        {--sync : Run synchronously (no queue, faster for <50k)}
        {--batch-size=500 : Number of emails per queue job}
        {--queue=default : Queue name to dispatch jobs on}
        {--batch-name= : Optional human-readable batch name}';

    protected $description = 'Verify email providers via MX lookup for a list of emails (100k+ supported)';

    public function handle(EmailProviderVerifier $verifier): int
    {
        $source = $this->argument('source');
        $column = $this->option('column');
        $delimiter = $this->option('delimiter');
        $smtpCheck = $this->option('smtp');
        $syncMode = $this->option('sync');
        $batchSize = (int) $this->option('batch-size');
        $queue = $this->option('queue');
        $batchName = $this->option('batch-name') ?: "verify-" . Str::random(8);

        $this->info("📧 Email Provider Verifier");
        $this->newLine();

        // Step 1: Parse file
        $this->info("📂 Parsing file: {$source}");

        $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));

        try {
            if ($ext === 'csv') {
                $parsed = $verifier->parseCsvEmails($source, $column, $delimiter);
            } elseif (in_array($ext, ['txt', 'text', 'list'])) {
                $parsed = $verifier->parseTxtEmails($source);
            } else {
                $parsed = $verifier->parseCsvEmails($source, $column, $delimiter);
            }
        } catch (\RuntimeException $e) {
            $this->error("❌ {$e->getMessage()}");
            return self::FAILURE;
        }

        $emails = $parsed['emails'];
        $totalLines = $parsed['total_lines'];
        $invalidLines = $parsed['invalid_lines'];

        $this->info("   Total lines: {$totalLines}");
        $this->info("   Valid emails: " . count($emails));
        $this->info("   Invalid lines: {$invalidLines}");

        if (empty($emails)) {
            $this->error("❌ No valid email addresses found.");
            return self::FAILURE;
        }

        $this->newLine();

        // Step 2: Deduplicate
        $uniqueEmails = array_values(array_unique(array_map('strtolower', $emails)));
        $duplicates = count($emails) - count($uniqueEmails);

        if ($duplicates > 0) {
            $this->info("🔄 Removed {$duplicates} duplicate emails");
        }

        $this->info("✅ Processing " . count($uniqueEmails) . " unique emails");
        $this->newLine();

        // Create a batch ID
        $verificationBatchId = 'vbatch_' . Str::random(12);

        if ($syncMode) {
            return $this->runSync($verifier, $uniqueEmails, $verificationBatchId, $smtpCheck, basename($source));
        }

        // Step 3: Queue mode
        $batches = array_chunk($uniqueEmails, $batchSize);
        $totalJobs = count($batches);

        $this->info("⚡ Dispatching {$totalJobs} jobs (batch size: {$batchSize})");
        $this->info("   Queue: {$queue}");

        if ($smtpCheck) {
            $this->warn("   ⚠ SMTP verification enabled — this will be slower");
        }

        $this->newLine();

        $bar = $this->output->createProgressBar($totalJobs);
        $bar->start();

        foreach ($batches as $chunk) {
            VerifyEmailProvidersJob::dispatch(
                $chunk,
                $verificationBatchId,
                $smtpCheck,
                basename($source),
            )->onQueue($queue);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("🎉 All jobs dispatched!");
        $this->newLine();

        $this->info("📊 Batch ID: {$verificationBatchId}");
        $this->newLine();
        $this->info("Commands:");
        $this->info("  email:verify:stats {$verificationBatchId}       — View summary");
        $this->info("  email:verify:export {$verificationBatchId}      — Export results to CSV");
        $this->info("  email:verify:by-provider {$verificationBatchId} — Filter by provider");
        $this->info("  email:smtp-verify {$verificationBatchId}          — SMTP active/inactive check");

        return self::SUCCESS;
    }

    private function runSync(
        EmailProviderVerifier $verifier,
        array $uniqueEmails,
        string $batchId,
        bool $smtpCheck,
        string $source
    ): int {
        $this->info("🔄 Running in SYNC mode (no queue)");
        $this->newLine();

        $total = count($uniqueEmails);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $results = ['verified' => 0, 'failed' => 0];
        $batchSize = 100;

        foreach (array_chunk($uniqueEmails, $batchSize) as $chunk) {
            $bulk = $verifier->verifyBulk($chunk, $smtpCheck);

            foreach ($bulk['results'] as $result) {
                EmailVerification::create([
                    'batch_id'             => $batchId,
                    'email'                => $result['email'],
                    'domain'               => $result['domain'],
                    'provider'             => $result['provider'],
                    'provider_label'       => $result['provider_label'],
                    'confidence'           => $result['confidence'],
                    'imap_host'            => $result['imap_host'],
                    'smtp_host'            => $result['smtp_host'],
                    'port'                 => $result['port'],
                    'mx_records'           => $result['mx_records'],
                    'smtp_checked'         => $smtpCheck,
                    'smtp_reachable'       => $result['smtp_reachable'],
                    'smtp_response_code'   => $result['smtp_response_code'],
                    'smtp_error'           => $result['smtp_error'],
                    'status'               => 'verified',
                    'verification_time_ms' => $result['verification_time_ms'],
                    'source'               => $source,
                ]);
                $results['verified']++;
            }

            foreach ($bulk['errors'] as $error) {
                EmailVerification::create([
                    'batch_id'  => $batchId,
                    'email'     => $error['email'],
                    'domain'    => $verifier->extractDomain($error['email']) ?? '',
                    'provider'  => 'unknown',
                    'provider_label' => 'Unknown',
                    'status'    => 'failed',
                    'error'     => $error['error'],
                    'source'    => $source,
                ]);
                $results['failed']++;
            }

            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Sync verification complete!");
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Verified', number_format($results['verified'])],
                ['Failed', number_format($results['failed'])],
                ['Total', number_format($total)],
            ]
        );

        $this->newLine();
        $this->info("📊 Batch ID: {$batchId}");
        $this->newLine();
        $this->info("Commands:");
        $this->info("  email:verify:stats {$batchId}       — View provider breakdown");
        $this->info("  email:smtp-verify {$batchId}          — SMTP active/inactive check");
        $this->info("  email:verify:export {$batchId}      — Export to CSV");

        return self::SUCCESS;
    }
}
