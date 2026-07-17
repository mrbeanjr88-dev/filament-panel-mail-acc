<?php

namespace App\Filament\Resources\EmailAccounts\Schemas;

use App\Services\OAuth\OAuthTokenService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EmailAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Display Name')
                        ->required()
                        ->placeholder('Personal Gmail'),

                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                            $p = static::detectProvider($state);
                            if ($p === null) {
                                return;
                            }
                            $set('host', $p['host']);
                            $set('port', $p['port']);
                            $set('encryption', $p['encryption']);
                            $set('authentication', $p['authentication']);
                            $set('hold_folder', $p['hold_folder']);
                            $set('validate_cert', $p['validate_cert']);
                            if (! filled($get('username'))) {
                                $set('username', $state);
                            }
                        }),

                    Html::make(fn (Get $get): HtmlString => static::providerBanner($get('email')))
                        ->visible(fn (Get $get): bool => static::detectProvider($get('email')) !== null)
                        ->columnSpanFull(),
                ]),

            Section::make('OAuth2 Connection')
                ->description('Connect via Google or Microsoft OAuth for modern email providers.')
                ->schema([
                    Html::make(function (Get $get): HtmlString {
                        $recordId = $get('recordId') ?? null;
                        if (! $recordId) {
                            return new HtmlString(static::oauthConnectButtons(null));
                        }
                        $account = \App\Models\EmailAccount::find($recordId);
                        if (! $account) {
                            return new HtmlString(static::oauthConnectButtons(null));
                        }
                        return new HtmlString(static::oauthStatusHtml($account));
                    })
                    ->columnSpanFull(),
                ])
                ->visible(function (Get $get): bool {
                    $email = $get('email') ?? '';
                    $domain = strtolower(trim(substr($email, strpos($email, '@') + 1)));
                    return in_array($domain, [
                        'gmail.com', 'googlemail.com',
                        'outlook.com', 'hotmail.com', 'live.com', 'msn.com',
                        'outlook.de', 'hotmail.ro', 'live.ro',
                    ]);
                }),

            Section::make('IMAP Connection')
                ->columns(3)
                ->schema([
                    TextInput::make('host')->required()->placeholder('imap.gmail.com'),
                    TextInput::make('port')->numeric()->required()->default(993),
                    Select::make('encryption')
                        ->options(['ssl' => 'SSL', 'tls' => 'TLS', 'starttls' => 'STARTTLS', 'none' => 'None'])
                        ->default('ssl')->required(),
                    TextInput::make('username')->label('Username')->required(),
                    TextInput::make('password')
                        ->label('Password / App Password')
                        ->password()->revealable()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->helperText('For OAuth accounts this field is managed automatically.')
                        ->columnSpan(2),
                    Select::make('authentication')
                        ->label('Authentication')
                        ->options(['' => 'Basic (password)', 'oauth' => 'OAuth2 (XOAUTH2)'])
                        ->default('')
                        ->helperText('Gmail/Outlook: use OAuth2 connect button above.'),
                    Toggle::make('validate_cert')->label('Validate certificate')->default(true),
                ]),

            Section::make('Folders')
                ->columns(4)
                ->schema([
                    TextInput::make('inbox_folder')->default('INBOX')->required(),
                    TextInput::make('hold_folder')
                        ->label('Quarantine (Hold)')
                        ->default('INBOX.Hold')
                        ->required()
                        ->helperText('Gmail: use "/" (e.g., INBOX/Hold).'),
                    TextInput::make('approved_folder')->label('Pending Approval')->default('INBOX')->required(),
                    TextInput::make('rejected_folder')->label('Rejected')->placeholder('INBOX.Trash'),
                ]),

            Section::make('Synchronization')
                ->columns(3)
                ->schema([
                    Toggle::make('is_active')->label('Active')->default(true),
                    Toggle::make('auto_sync')->label('Auto Sync')->default(true),
                    TextInput::make('fetch_limit')->label('Max/run')->numeric()->default(50),
                ]),
        ]);
    }

    private static function oauthConnectButtons(?int $accountId): string
    {
        $googleUrl = route('oauth.connect', ['provider' => 'google', 'account_id' => $accountId]);
        $msUrl = route('oauth.connect', ['provider' => 'microsoft', 'account_id' => $accountId]);

        return <<<HTML
<div class="flex flex-wrap gap-3">
    <a href="{$googleUrl}" class="fi-btn fi-btn-size-sm inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-700">
        <svg class="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
        Connect Gmail (Google)
    </a>
    <a href="{$msUrl}" class="fi-btn fi-btn-size-sm inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-700">
        <svg class="h-5 w-5" viewBox="0 0 23 23"><rect fill="#F25022" x="1" y="1" width="10" height="10"/><rect fill="#7FBA00" x="12" y="1" width="10" height="10"/><rect fill="#00A4EF" x="1" y="12" width="10" height="10"/><rect fill="#FFB900" x="12" y="12" width="10" height="10"/></svg>
        Connect Outlook (Microsoft)
    </a>
</div>
HTML;
    }

    private static function oauthStatusHtml(\App\Models\EmailAccount $account): string
    {
        $html = '';

        if ($account->authentication === 'oauth' && $account->oauth_provider) {
            $provider = ucfirst($account->oauth_provider);
            $expires = $account->oauth_expires_at;
            $isExpired = $expires && $expires->isPast();
            $hasRefresh = filled($account->oauth_refresh_token) ? 'Yes' : 'No';

            $statusColor = $isExpired ? 'warning' : 'success';
            $statusText = $isExpired ? 'Token expired (will auto-refresh)' : 'Connected';
            $expiryText = $expires ? $expires->diffForHumans() : 'unknown';

            $disconnectUrl = route('oauth.disconnect', [
                'provider'  => $account->oauth_provider,
                'accountId' => $account->id,
            ]);

            $html .= <<<HTML
<div class="rounded-lg border border-success-300 bg-success-50 px-4 py-3 text-sm text-success-800 dark:border-success-700 dark:bg-success-950 dark:text-success-200">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 text-success-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            <span class="font-semibold">{$provider} OAuth2: {$statusText}</span>
            <span class="opacity-70">— Token expires {$expiryText} · Refresh: {$hasRefresh}</span>
        </div>
    </div>
</div>
HTML;
        } else {
            $html .= static::oauthConnectButtons($account->id ?? null);
        }

        return $html;
    }

    private static function detectProvider(?string $email): ?array
    {
        if (! $email || ! str_contains($email, '@')) {
            return null;
        }

        $domain = strtolower(trim(substr($email, strpos($email, '@') + 1)));

        return match (true) {
            in_array($domain, ['gmail.com', 'googlemail.com']) => [
                'host' => 'imap.gmail.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX/Hold', 'validate_cert' => true,
                'label' => 'Gmail',
                'note'  => 'Use the <strong>OAuth2 Connect</strong> button below, or generate an App Password if you prefer basic auth.',
            ],
            in_array($domain, ['yahoo.com', 'yahoo.ro', 'yahoo.co.uk', 'yahoo.fr', 'yahoo.de', 'ymail.com']) => [
                'host' => 'imap.mail.yahoo.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX/Hold', 'validate_cert' => true,
                'label' => 'Yahoo Mail',
                'note'  => 'Requires an <strong>App Password</strong> from Yahoo security settings.',
            ],
            in_array($domain, ['outlook.com', 'hotmail.com', 'live.com', 'msn.com', 'hotmail.ro', 'live.ro']) => [
                'host' => 'outlook.office365.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX/Hold', 'validate_cert' => true,
                'label' => 'Outlook / Hotmail',
                'note'  => 'Use the <strong>OAuth2 Connect</strong> button below for the easiest setup.',
            ],
            in_array($domain, ['icloud.com', 'me.com', 'mac.com']) => [
                'host' => 'imap.mail.me.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX/Hold', 'validate_cert' => true,
                'label' => 'iCloud Mail',
                'note'  => 'Requires an <strong>App-Specific Password</strong> from <em>appleid.apple.com</em>.',
            ],
            in_array($domain, ['protonmail.com', 'proton.me', 'pm.me']) => [
                'host' => '127.0.0.1', 'port' => 1143, 'encryption' => 'tls',
                'authentication' => '', 'hold_folder' => 'Hold', 'validate_cert' => false,
                'label' => 'ProtonMail',
                'note'  => 'Requires <strong>Proton Mail Bridge</strong> running on localhost:1143.',
            ],
            in_array($domain, ['zoho.com', 'zohomail.com']) => [
                'host' => 'imap.zoho.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX/Hold', 'validate_cert' => true,
                'label' => 'Zoho Mail',
                'note'  => 'Enable IMAP from Zoho Mail → Settings → Mail Accounts → IMAP Access.',
            ],
            in_array($domain, ['fastmail.com', 'fastmail.fm', 'fastmail.org']) => [
                'host' => 'imap.fastmail.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX/Hold', 'validate_cert' => true,
                'label' => 'Fastmail',
                'note'  => 'Generate an <strong>App Password</strong> from Settings.',
            ],
            in_array($domain, ['rcs-rds.ro', 'digi.net']) => [
                'host' => 'imap.rcs-rds.ro', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX/Hold', 'validate_cert' => true,
                'label' => 'RCS-RDS / Digi',
                'note'  => null,
            ],
            default => null,
        };
    }

    private static function providerBanner(?string $email): HtmlString
    {
        $p = static::detectProvider($email);
        if ($p === null) {
            return new HtmlString('');
        }

        $label = htmlspecialchars($p['label'], ENT_QUOTES, 'UTF-8');
        $html  = '<div class="rounded-lg border border-info-300 bg-info-50 px-4 py-3 text-sm text-info-800 dark:border-info-700 dark:bg-info-950 dark:text-info-200">'
            . '<div class="flex items-center gap-2 font-semibold mb-1">'
            . '<svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>'
            . 'Detected provider: ' . $label . ' — IMAP settings have been auto-filled.'
            . '</div>';

        if (! empty($p['note'])) {
            $html .= '<div class="mt-1 opacity-80">ℹ&nbsp;' . $p['note'] . '</div>';
        }

        $html .= '</div>';

        return new HtmlString($html);
    }
}
