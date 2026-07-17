<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Services\OAuth\OAuthTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Token\AccessToken;

class OAuthController extends Controller
{
    public function __construct(
        private OAuthTokenService $oauth,
    ) {}

    public function connect(string $provider, Request $request): RedirectResponse
    {
        $accountId = $request->query('account_id');

        if (! in_array($provider, ['google', 'microsoft'])) {
            return redirect()->route('filament.admin.pages.dashboard')
                ->withErrors(['Invalid OAuth provider.']);
        }

        $config = config("email-customs.oauth.{$provider}");
        if (empty($config['client_id']) || empty($config['client_secret'])) {
            return redirect()->route('filament.admin.pages.dashboard')
                ->withErrors(["OAuth for {$provider} is not configured. Add GOOGLE_CLIENT_ID/SECRET or MS_CLIENT_ID/SECRET to .env"]);
        }

        $request->session()->put('oauth_provider', $provider);
        $request->session()->put('oauth_account_id', $accountId);

        return redirect($this->oauth->getAuthorizationUrl($provider));
    }

    public function callback(string $provider, Request $request): RedirectResponse
    {
        $error = $request->query('error');
        if ($error) {
            return redirect()->route('filament.admin.pages.dashboard')
                ->withErrors(["OAuth denied: {$error}"]);
        }

        $code = $request->query('code');
        if (! $code) {
            return redirect()->route('filament.admin.pages.dashboard')
                ->withErrors(['No authorization code received.']);
        }

        $storedProvider = $request->session()->pull('oauth_provider');
        $accountId = $request->session()->pull('oauth_account_id');

        if ($storedProvider !== $provider) {
            return redirect()->route('filament.admin.pages.dashboard')
                ->withErrors(['OAuth provider mismatch.']);
        }

        try {
            $token = $this->oauth->exchangeCode($provider, $code);

            if ($accountId) {
                $account = EmailAccount::findOrFail($accountId);
            } else {
                $email = $this->extractEmailFromToken($token, $provider);
                $account = EmailAccount::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'     => $email,
                        'host'     => $provider === 'google' ? 'imap.gmail.com' : 'outlook.office365.com',
                        'port'     => 993,
                        'encryption' => 'ssl',
                        'validate_cert' => true,
                        'protocol' => 'imap',
                        'username' => $email,
                        'inbox_folder'    => 'INBOX',
                        'hold_folder'     => 'INBOX/Hold',
                        'approved_folder' => 'INBOX',
                    ]
                );
            }

            $this->oauth->storeToken($account, $provider, $token);

            \Filament\Notifications\Notification::make()
                ->title('Connected successfully')
                ->body("OAuth2 {$provider} token saved for {$account->email}")
                ->success()
                ->send();

            return redirect()->route('filament.admin.resources.email-accounts.edit', ['record' => $account->id]);
        } catch (\Exception $e) {
            Log::error("OAuth callback failed for {$provider}", ['error' => $e->getMessage()]);
            return redirect()->route('filament.admin.pages.dashboard')
                ->withErrors(["OAuth connection failed: {$e->getMessage()}"]);
        }
    }

    public function disconnect(string $provider, int $accountId): RedirectResponse
    {
        $account = EmailAccount::findOrFail($accountId);
        $account->update([
            'oauth_access_token'  => null,
            'oauth_refresh_token' => null,
            'oauth_expires_at'    => null,
            'oauth_provider'      => null,
            'authentication'      => null,
            'password'            => null,
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Disconnected')
            ->body("OAuth2 {$provider} disconnected from {$account->email}")
            ->success()
            ->send();

        return redirect()->route('filament.admin.resources.email-accounts.edit', ['record' => $accountId]);
    }

    private function extractEmailFromToken(AccessToken $token, string $provider): string
    {
        $claims = $token->getValues();

        if ($provider === 'google' && isset($claims['id_token'])) {
            $payload = json_decode(base64_decode(explode('.', $claims['id_token'])[1]), true);
            return $payload['email'] ?? 'unknown@gmail.com';
        }

        if ($provider === 'microsoft') {
            $payload = json_decode(base64_decode(explode('.', $claims['id_token'] ?? '')[1] ?? ''), true);
            return $payload['preferred_username'] ?? $payload['email'] ?? 'unknown@outlook.com';
        }

        return 'unknown@example.com';
    }
}
