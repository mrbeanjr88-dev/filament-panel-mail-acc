<?php

namespace Tests\Unit\Services;

use App\Models\EmailAccount;
use App\Models\EmailFilterRule;
use App\Models\PendingEmail;
use App\Services\Imap\RuleEngine;
use Tests\TestCase;

class RuleEngineTest extends TestCase
{
    protected RuleEngine $engine;
    protected EmailAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(RuleEngine::class);
        $this->account = EmailAccount::factory()->create();
    }

    public function test_matches_from_contains()
    {
        $rule = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_contains' => 'sender@example.com',
            'is_active' => true,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'sender@example.com',
        ]);

        $matched = $this->engine->apply($email);

        $this->assertNotNull($matched);
        $this->assertEquals($rule->id, $matched->id);
    }

    public function test_matches_subject_contains()
    {
        $rule = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'subject_contains' => 'Invoice',
            'is_active' => true,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'subject' => 'Monthly Invoice #001',
        ]);

        $matched = $this->engine->apply($email);

        $this->assertNotNull($matched);
        $this->assertEquals($rule->id, $matched->id);
    }

    public function test_matches_amount_range()
    {
        $rule = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'amount_min' => 100,
            'amount_max' => 1000,
            'is_active' => true,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'extracted_amount' => 500,
        ]);

        $matched = $this->engine->apply($email);

        $this->assertNotNull($matched);
        $this->assertEquals($rule->id, $matched->id);
    }

    public function test_ignores_inactive_rules()
    {
        EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_contains' => 'sender@example.com',
            'is_active' => false,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'sender@example.com',
        ]);

        $matched = $this->engine->apply($email);

        $this->assertNull($matched);
    }

    public function test_ignores_different_account_rules()
    {
        $otherAccount = EmailAccount::factory()->create();

        EmailFilterRule::factory()->create([
            'email_account_id' => $otherAccount->id,
            'from_contains' => 'sender@example.com',
            'is_active' => true,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'sender@example.com',
        ]);

        $matched = $this->engine->apply($email);

        $this->assertNull($matched);
    }

    public function test_match_type_all_requires_all_conditions()
    {
        $rule = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_contains' => 'sender@example.com',
            'subject_contains' => 'Invoice',
            'match_type' => 'all',
            'is_active' => true,
        ]);

        // Email matches both conditions
        $email1 = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'sender@example.com',
            'subject' => 'Invoice #001',
        ]);
        $matched1 = $this->engine->apply($email1);
        $this->assertNotNull($matched1);

        // Email matches only one condition
        $email2 = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'sender@example.com',
            'subject' => 'Monthly Report',
        ]);
        $matched2 = $this->engine->apply($email2);
        $this->assertNull($matched2);
    }

    public function test_match_type_any_requires_one_condition()
    {
        $rule = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_contains' => 'sender@example.com',
            'subject_contains' => 'Invoice',
            'match_type' => 'any',
            'is_active' => true,
        ]);

        // Email matches one condition
        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'sender@example.com',
            'subject' => 'Monthly Report',
        ]);

        $matched = $this->engine->apply($email);

        $this->assertNotNull($matched);
        $this->assertEquals($rule->id, $matched->id);
    }

    public function test_applies_actions_to_email()
    {
        $rule = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_contains' => 'test',
            'then_category' => 'transaction',
            'then_tag' => 'important',
            'is_active' => true,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'test@example.com',
            'category' => null,
            'tag' => null,
        ]);

        $this->engine->apply($email);
        $email->refresh();

        $this->assertEquals('transaction', $email->category?->value);
        $this->assertEquals('important', $email->tag);
        $this->assertEquals($rule->id, $email->matched_rule_id);
    }

    public function test_stops_processing_if_stop_flag_set()
    {
        $rule1 = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_contains' => 'test',
            'stop_processing' => true,
            'priority' => 1,
            'is_active' => true,
        ]);

        EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_contains' => 'test',
            'priority' => 2,
            'is_active' => true,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'test@example.com',
        ]);

        $matched = $this->engine->apply($email);

        $this->assertEquals($rule1->id, $matched->id);
    }

    public function test_invalid_regex_fails_gracefully()
    {
        // Create rule with invalid regex pattern
        $rule = EmailFilterRule::factory()->create([
            'email_account_id' => $this->account->id,
            'from_regex' => '[invalid(regex',
            'is_active' => true,
        ]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'from_email' => 'test@example.com',
        ]);

        // Should not match (invalid regex returns false)
        $matched = $this->engine->apply($email);

        $this->assertNull($matched);
    }
}
