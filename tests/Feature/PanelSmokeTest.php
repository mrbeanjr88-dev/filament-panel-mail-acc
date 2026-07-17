<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\EmailAccount;
use App\Models\EmailFilterRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_panel_pages_render(): void
    {
        $user = User::create([
            'name' => 'Smoke', 'email' => 'smoke@test.local', 'password' => bcrypt('secret'),
        ]);

        // date minime ca tabelele/relațiile să aibă ceva de randat
        $acc = EmailAccount::create([
            'name' => 'Test', 'email' => 't@e.com', 'host' => 'imap.e.com',
            'username' => 'u', 'password' => 'p',
        ]);
        BankAccount::create(['label' => 'BT RON', 'currency' => 'RON', 'current_balance' => 100]);
        EmailFilterRule::create(['name' => 'R1', 'subject_contains' => 'x']);

        $urls = [
            '/admin',
            '/admin/pending-emails',
            '/admin/bank-accounts',
            '/admin/bank-accounts/create',
            '/admin/email-accounts',
            '/admin/email-accounts/create',
            "/admin/email-accounts/{$acc->id}/edit",
            '/admin/email-filter-rules',
            '/admin/email-filter-rules/create',
            '/admin/manage-settings',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($user)->get($url);
            $this->assertContains(
                $response->getStatusCode(),
                [200],
                "URL {$url} a întors {$response->getStatusCode()}"
            );
        }
    }
}
