<?php

namespace App\Filament\Resources\EmailAccounts\Schemas;

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
                        ->label('Password / app-password')
                        ->password()->revealable()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->columnSpan(2),
                    Select::make('authentication')
                        ->label('Authentication')
                        ->options(['' => 'Basic (password)', 'oauth' => 'OAuth2 (XOAUTH2)'])
                        ->default('')
                        ->helperText('Modern Gmail/Outlook requires OAuth2.'),
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

    // ── Provider detection ────────────────────────────────────────────────────

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
                'note'  => 'Requires an <strong>App Password</strong> (not the account password) — enable 2FA and generate one from <em>myaccount.google.com/apppasswords</em>. The Hold folder uses "/" not "."',
            ],
            in_array($domain, ['yahoo.com', 'yahoo.ro', 'yahoo.co.uk', 'yahoo.fr', 'yahoo.de', 'ymail.com']) => [
                'host' => 'imap.mail.yahoo.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX.Hold', 'validate_cert' => true,
                'label' => 'Yahoo Mail',
                'note'  => 'Requires an <strong>App Password</strong> from Yahoo security settings (<em>Account Security → Generate app password</em>).',
            ],
            in_array($domain, ['outlook.com', 'hotmail.com', 'live.com', 'msn.com', 'hotmail.ro', 'live.ro']) => [
                'host' => 'imap-mail.outlook.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX.Hold', 'validate_cert' => true,
                'label' => 'Outlook / Hotmail',
                'note'  => 'Enable IMAP from Outlook → Settings → Mail → Sync email. Business Microsoft 365 accounts require permissions from the Admin Center.',
            ],
            in_array($domain, ['icloud.com', 'me.com', 'mac.com']) => [
                'host' => 'imap.mail.me.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX.Hold', 'validate_cert' => true,
                'label' => 'iCloud Mail',
                'note'  => 'Requires an <strong>App-Specific Password</strong> from <em>appleid.apple.com → Sign-In and Security</em>.',
            ],
            in_array($domain, ['protonmail.com', 'proton.me', 'pm.me']) => [
                'host' => '127.0.0.1', 'port' => 1143, 'encryption' => 'tls',
                'authentication' => '', 'hold_folder' => 'Hold', 'validate_cert' => false,
                'label' => 'ProtonMail',
                'note'  => 'Requires <strong>Proton Mail Bridge</strong> running on localhost:1143. Download from <em>proton.me/mail/bridge</em>.',
            ],
            in_array($domain, ['zoho.com', 'zohomail.com']) => [
                'host' => 'imap.zoho.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX.Hold', 'validate_cert' => true,
                'label' => 'Zoho Mail',
                'note'  => 'Enable IMAP from Zoho Mail → Settings → Mail Accounts → IMAP Access.',
            ],
            in_array($domain, ['fastmail.com', 'fastmail.fm', 'fastmail.org']) => [
                'host' => 'imap.fastmail.com', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX.Hold', 'validate_cert' => true,
                'label' => 'Fastmail',
                'note'  => 'Generate an <strong>App Password</strong> from <em>Settings → Privacy & Security</em>.',
            ],
            in_array($domain, ['rcs-rds.ro', 'digi.net']) => [
                'host' => 'imap.rcs-rds.ro', 'port' => 993, 'encryption' => 'ssl',
                'authentication' => '', 'hold_folder' => 'INBOX.Hold', 'validate_cert' => true,
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
