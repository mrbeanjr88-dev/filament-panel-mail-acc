<?php

namespace App\Console\Commands;

use App\Models\EmailVerification;
use App\Services\EmailProviderVerifier;
use Illuminate\Console\Command;

class SmtpVerifyBatch extends Command
{
    protected $signature = 'email:smtp-verify
        {batchId : The batch ID to run verification on}
        {--limit= : Max emails to verify (leave empty for all)}
        {--provider= : Only verify emails from this provider}
        {--force : Re-verify already checked emails}';

    protected $description = 'Verify email activity via DNS checks (MX exists, A record resolves, known provider)';

    private const MAJOR_PROVIDERS = [
        'google', 'microsoft', 'yahoo', 'zoho', 'protonmail',
        'yandex', 'icloud', 'gmx', 'fastmail', 'aol', 'tutanota',
        'mail_ru', 'qq', 'naver', 'web_de', 'mail_com', 'ionos',
        'telekom', 'freenet', 'vodafone_de', 'seznam', 'mail_de',
        'libero', 'netcologne', 'skynet', 'hetnet', 'mail_eu',
        'comcast', 'att', 'virginmedia',
        'a1_austria', 'drei_austria', 'swisscom', 'proximus',
        'telenet_be', 'free_fr', 'orange_fr', 'sfr_fr',
        'tim_italy', 'virgilio', 'aruba',
    ];

    public function handle(EmailProviderVerifier $verifier): int
    {
        $batchId = $this->argument('batchId');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $providerFilter = $this->option('provider');
        $force = $this->option('force');

        $this->info("📬 Email Activity Verifier (DNS-based)");
        $this->info("   Batch: {$batchId}");
        $this->newLine();

        $query = EmailVerification::where('batch_id', $batchId)
            ->where('status', 'verified');

        if (!$force) {
            $query->where('smtp_checked', false);
        }

        if ($providerFilter) {
            $query->where('provider', $providerFilter);
        }

        $total = $query->count();
        if ($total === 0) {
            $this->error("❌ No unverified emails found.");
            return self::FAILURE;
        }

        if ($limit) {
            $total = min($total, $limit);
        }

        $this->info("📧 Emails to verify: {$total}");
        $this->newLine();

        // Build domain list from records, resolving on-the-fly per unique domain
        $emailRecords = $query->limit($limit)->get();
        $domainsToResolve = $emailRecords->pluck('domain')->unique()->toArray();
        $domainCache = [];

        // Check how many are already cached from previous runs
        $cachedCount = 0;
        foreach ($domainsToResolve as $d) {
            if (\Illuminate\Support\Facades\Cache::has("mx_lookup_{$d}")) {
                $cachedCount++;
            }
        }

        $uncached = count($domainsToResolve) - $cachedCount;
        $this->info("🔍 Resolving " . count($domainsToResolve) . " domains ({$cachedCount} cached, {$uncached} new)...");
        if ($uncached > 100) {
            $this->warn("   ⚠ {$uncached} new DNS lookups — may take a few minutes due to rate limits");
        }

        $resolveBar = $this->output->createProgressBar(count($domainsToResolve));
        $resolveBar->start();

        $dnsErrors = 0;
        foreach ($domainsToResolve as $i => $domain) {
            try {
                $mxData = $verifier->lookupMx($domain);
                $domainCache[$domain] = [
                    'mx_hosts'  => $mxData['mx_hosts'],
                    'has_mx'    => !empty($mxData['mx_hosts']),
                ];
            } catch (\Throwable $e) {
                $dnsErrors++;
                $domainCache[$domain] = [
                    'mx_hosts'  => [],
                    'has_mx'    => false,
                ];
            }

            // Small delay every 50 lookups to avoid rate limits on uncached domains
            if (!$cachedCount && $i > 0 && $i % 50 === 0) {
                usleep(100000); // 100ms
            }

            $resolveBar->advance();
        }

        $resolveBar->finish();
        $this->newLine(2);

        // Classify each email
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $stats = ['active' => 0, 'configured' => 0, 'inactive' => 0, 'no_mx' => 0];

        foreach ($emailRecords as $record) {
            $domain = $record->domain;
            $provider = $record->provider;
            $cache = $domainCache[$domain] ?? ['has_mx' => false, 'has_a' => false, 'mx_hosts' => [], 'a_records' => []];

            $activity = $this->classifyActivity($provider, $cache);

            $record->update([
                'smtp_checked'       => true,
                'smtp_reachable'     => $activity['reachable'],
                'smtp_response_code' => $activity['code'],
                'smtp_error'         => $activity['reason'],
                'smtp_mx_host'       => $cache['mx_hosts'][0] ?? null,
            ]);

            $stats[$activity['category']]++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("✅ Activity Verification Complete!");
        $this->newLine();

        $activePct = $total > 0 ? round(($stats['active'] / $total) * 100, 1) : 0;
        $configuredPct = $total > 0 ? round(($stats['configured'] / $total) * 100, 1) : 0;
        $inactivePct = $total > 0 ? round((($stats['inactive'] + $stats['no_mx']) / $total) * 100, 1) : 0;

        $this->table(
            ['Status', 'Count', 'Percentage', 'Meaning'],
            [
                ['✅ Active', number_format($stats['active']), "{$activePct}%", 'Known major provider — almost certainly active'],
                ['⚠️  Configured', number_format($stats['configured']), "{$configuredPct}%", 'MX records found — domain accepts email'],
                ['❌ Inactive', number_format($stats['inactive'] + $stats['no_mx']), "{$inactivePct}%", 'No MX records — domain likely does not accept email'],
            ]
        );

        // By provider
        $this->newLine();
        $this->info("📊 Activity by Provider:");
        $providerStats = EmailVerification::where('batch_id', $batchId)
            ->where('smtp_checked', true)
            ->selectRaw('
                provider, provider_label,
                COUNT(*) as total,
                SUM(CASE WHEN smtp_reachable = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN smtp_reachable = 0 THEN 1 ELSE 0 END) as inactive
            ')
            ->groupBy('provider', 'provider_label')
            ->orderByDesc('total')
            ->get();

        $this->table(
            ['Provider', 'Label', 'Total', 'Active', 'Inactive', 'Active %'],
            $providerStats->map(fn($s) => [
                $s->provider,
                $s->provider_label,
                number_format($s->total),
                number_format($s->active),
                number_format($s->inactive),
                $s->total > 0 ? round(($s->active / $s->total) * 100, 1) . '%' : '-',
            ])
        );

        // Top inactive domains
        $this->newLine();
        $this->info("❌ Top Inactive Domains:");
        $inactiveDomains = EmailVerification::where('batch_id', $batchId)
            ->where('smtp_checked', true)
            ->where('smtp_reachable', false)
            ->selectRaw('domain, COUNT(*) as count')
            ->groupBy('domain')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        $this->table(
            ['Domain', 'Inactive Count', 'Provider'],
            $inactiveDomains->map(fn($d) => [
                $d->domain,
                number_format($d->count),
                EmailVerification::where('batch_id', $batchId)->where('domain', $d->domain)->value('provider'),
            ])
        );

        $this->newLine();
        $this->info("Export active emails: email:verify:export {$batchId}");

        return self::SUCCESS;
    }

    private function classifyActivity(string $provider, array $cache): array
    {
        // Known major providers → almost certainly active
        if (in_array($provider, self::MAJOR_PROVIDERS)) {
            return [
                'category'  => 'active',
                'reachable' => true,
                'code'      => 250,
                'reason'    => 'Known major provider',
            ];
        }

        // Has MX records → domain is configured to accept email
        if ($cache['has_mx']) {
            return [
                'category'  => 'configured',
                'reachable' => true,
                'code'      => 250,
                'reason'    => 'MX records present',
            ];
        }

        // No MX records → likely inactive
        return [
            'category'  => 'inactive',
            'reachable' => false,
            'code'      => 0,
            'reason'    => 'No MX records found',
        ];
    }
}
