<?php

namespace App\Services\Imap;

use App\Models\EmailAccount;
use App\Services\OAuth\OAuthTokenService;
use Webklex\IMAP\Facades\Client as ClientManager;
use Webklex\PHPIMAP\Client;

class ImapClientFactory
{
    public function for(EmailAccount $account): Client
    {
        $password = $account->password;

        if ($account->authentication === 'oauth') {
            $oauthService = app(OAuthTokenService::class);
            $password = $oauthService->ensureValidToken($account);
        }

        /** @var Client $client */
        $client = ClientManager::make([
            'host'           => $account->host,
            'port'           => $account->port,
            'protocol'       => $account->protocol ?: 'imap',
            'encryption'     => $this->normalizeEncryption($account->encryption),
            'validate_cert'  => (bool) $account->validate_cert,
            'username'       => $account->username,
            'password'       => $password,
            'authentication' => $account->authentication ?: null,
            'timeout'        => 30,
        ]);

        $client->connect();

        return $client;
    }

    private function normalizeEncryption(?string $encryption): string|false
    {
        return match (strtolower((string) $encryption)) {
            'ssl'      => 'ssl',
            'tls'      => 'tls',
            'starttls' => 'starttls',
            default    => false,
        };
    }
}
