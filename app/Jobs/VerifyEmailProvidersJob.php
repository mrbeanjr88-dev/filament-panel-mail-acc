<?php

namespace App\Jobs;

use App\Models\EmailVerification;
use App\Services\EmailProviderVerifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyEmailProvidersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public int $maxExceptions = 5;

    /**
     * @var array List of email addresses to verify in this job.
     */
    public array $emails;

    /**
     * @var string Batch ID for grouping results.
     */
    public string $batchId;

    /**
     * @var bool Whether to perform SMTP verification.
     */
    public bool $smtpCheck;

    /**
     * @var string Source identifier (e.g., filename, 'manual', 'api').
     */
    public string $source;

    public function __construct(
        array $emails,
        string $batchId,
        bool $smtpCheck = false,
        string $source = 'csv',
    ) {
        $this->emails = $emails;
        $this->batchId = $batchId;
        $this->smtpCheck = $smtpCheck;
        $this->source = $source;
    }

    public function handle(EmailProviderVerifier $verifier): void
    {
        $results = $verifier->verifyBulk($this->emails, $this->smtpCheck);

        foreach ($results['results'] as $result) {
            EmailVerification::create([
                'batch_id'             => $this->batchId,
                'email'                => $result['email'],
                'domain'               => $result['domain'],
                'provider'             => $result['provider'],
                'provider_label'       => $result['provider_label'],
                'confidence'           => $result['confidence'],
                'imap_host'            => $result['imap_host'],
                'smtp_host'            => $result['smtp_host'],
                'port'                 => $result['port'],
                'mx_records'           => $result['mx_records'],
                'smtp_checked'         => $this->smtpCheck,
                'smtp_reachable'       => $result['smtp_reachable'],
                'smtp_response_code'   => $result['smtp_response_code'],
                'smtp_error'           => $result['smtp_error'],
                'status'               => 'verified',
                'verification_time_ms' => $result['verification_time_ms'],
                'source'               => $this->source,
            ]);
        }

        foreach ($results['errors'] as $error) {
            EmailVerification::create([
                'batch_id'  => $this->batchId,
                'email'     => $error['email'],
                'domain'    => $verifier->extractDomain($error['email']) ?? '',
                'provider'  => 'unknown',
                'provider_label' => 'Unknown',
                'status'    => 'failed',
                'error'     => $error['error'],
                'source'    => $this->source,
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        // Log but don't crash the batch
        \Log::error("VerifyEmailProvidersJob failed", [
            'batch_id' => $this->batchId,
            'emails'   => $this->emails,
            'error'    => $exception->getMessage(),
        ]);
    }
}
