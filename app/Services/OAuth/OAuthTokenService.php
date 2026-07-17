<?php

namespace App\Services\OAuth;

use App\Models\EmailAccount;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;

class OAuthTokenService
{
    private const PROVIDERS = [
        'google' => [
            'authorize_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token_url'     => 'https://oauth2.googleapis.com/token',
            'scope'         => 'https://mail.google.com/',
            'extra'         => ['access_type' => 'offline', 'prompt' => 'consent'],
        ],
        'microsoft' => [
            'authorize_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'token_url'     => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'scope'         => 'https://outlook.office365.com/IMAP.AccessAsUser.All offline_access',
            'extra'         => [],
        ],
    ];

    public function getProvider(string $provider): GenericProvider
    {
        $config = self::PROVIDERS[$provider] ?? throw new \InvalidArgumentException("Unknown provider: {$provider}");

        $clientId     = config("email-customs.oauth.{$provider}.client_id");
        $clientSecret = config("email-customs.oauth.{$provider}.client_secret");
        $redirectUri  = config("email-customs.oauth.{$provider}.redirect");

        return new GenericProvider([
            'clientId'                => $clientId,
            'clientSecret'            => $clientSecret,
            'redirectUri'             => $redirectUri,
            'urlAuthorize'            => $config['authorize_url'],
            'urlAccessToken'          => $config['token_url'],
            'scopes'                  => $config['scope'],
        ]);
    }

    public function getAuthorizationUrl(string $provider): string
    {
        $providerObj = $this->getProvider($provider);
        $config = self::PROVIDERS[$provider];

        $params = array_merge([
            'scope' => $config['scope'],
        ], $config['extra']);

        return $providerObj->getAuthorizationUrl($params);
    }

    public function exchangeCode(string $provider, string $code): AccessToken
    {
        $providerObj = $this->getProvider($provider);
        return $providerObj->getAccessToken('authorization_code', ['code' => $code]);
    }

    public function refreshToken(string $provider, string $refreshToken): AccessToken
    {
        $providerObj = $this->getProvider($provider);
        return $providerObj->getAccessToken('refresh_token', ['refresh_token' => $refreshToken]);
    }

    public function storeToken(EmailAccount $account, string $provider, AccessToken $token): void
    {
        $account->update([
            'authentication'       => 'oauth',
            'oauth_provider'       => $provider,
            'oauth_access_token'   => $token->getToken(),
            'oauth_refresh_token'  => $token->getRefreshToken() ?? $account->oauth_refresh_token,
            'oauth_expires_at'     => $token->getExpires()
                ? \Carbon\Carbon::createFromTimestamp($token->getExpires())
                : now()->addHours(1),
            'password'             => $token->getToken(),
        ]);
    }

    public function ensureValidToken(EmailAccount $account): ?string
    {
        if ($account->authentication !== 'oauth' || empty($account->oauth_refresh_token)) {
            return $account->password;
        }

        $expiresAt = $account->oauth_expires_at;
        if ($expiresAt && $expiresAt->isFuture() && $expiresAt->diffInMinutes() > 5) {
            return $account->oauth_access_token;
        }

        try {
            $token = $this->refreshToken($account->oauth_provider, $account->oauth_refresh_token);
            $this->storeToken($account, $account->oauth_provider, $token);
            return $token->getToken();
        } catch (\Exception $e) {
            report($e);
            $account->update(['last_error' => 'OAuth token refresh failed: ' . $e->getMessage()]);
            return $account->oauth_access_token;
        }
    }
}
