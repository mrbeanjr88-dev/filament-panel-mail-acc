<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\EmailAccount;
use App\Models\PendingEmail;
use App\Models\User;
use App\Services\Imap\EmailApprovalService;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    protected EmailApprovalService $service;
    protected EmailAccount $account;
    protected BankAccount $bank;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EmailApprovalService::class);
        $this->account = EmailAccount::factory()->create();
        $this->bank = BankAccount::factory()->create();
        $this->operator = User::factory()->create();
    }

    public function test_approve_email_updates_status()
    {
        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'status' => 'pending',
        ]);

        $this->service->approve($email, $this->operator->email);

        $email->refresh();
        $this->assertEquals('processed', $email->status);
        $this->assertEquals($this->operator->email, $email->approved_by);
        $this->assertNotNull($email->approved_at);
    }

    public function test_reject_email_updates_status()
    {
        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'status' => 'pending',
        ]);

        $this->service->reject($email, $this->operator->email);

        $email->refresh();
        $this->assertEquals('rejected', $email->status);
        $this->assertNotNull($email->rejected_at);
    }

    public function test_approve_with_bank_update_applies_balance()
    {
        $initialBalance = 10000.0;
        $this->bank->update(['current_balance' => $initialBalance]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'bank_account_id' => $this->bank->id,
            'status' => 'pending',
            'extracted_amount' => 500,
            'extracted_balance' => null,
            'extracted_direction' => 'debit',
        ]);

        $this->service->approveWithBankUpdate($email, $this->operator->email);

        $email->refresh();
        $this->bank->refresh();

        $this->assertTrue($email->applied_to_bank);
        $this->assertEquals('processed', $email->status);
        $this->assertEquals($initialBalance - 500, $this->bank->current_balance);
    }

    public function test_approve_with_bank_update_uses_override_amount()
    {
        $initialBalance = 10000.0;
        $this->bank->update(['current_balance' => $initialBalance]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'bank_account_id' => $this->bank->id,
            'status' => 'pending',
            'extracted_amount' => 500,
            'extracted_direction' => 'debit',
        ]);

        $this->service->approveWithBankUpdate(
            $email,
            $this->operator->email,
            null,
            1000, // Override amount
            null,
            null
        );

        $this->bank->refresh();
        $this->assertEquals($initialBalance - 1000, $this->bank->current_balance);
    }

    public function test_approve_with_bank_update_uses_override_balance()
    {
        $initialBalance = 10000.0;
        $this->bank->update(['current_balance' => $initialBalance]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'bank_account_id' => $this->bank->id,
            'status' => 'pending',
            'extracted_balance' => 5000,
        ]);

        $this->service->approveWithBankUpdate(
            $email,
            $this->operator->email,
            null,
            null,
            8000, // Override balance
            null
        );

        $this->bank->refresh();
        $this->assertEquals(8000, $this->bank->current_balance);
    }

    public function test_approve_with_bank_update_uses_different_bank_account()
    {
        $bank1 = BankAccount::factory()->create(['current_balance' => 10000]);
        $bank2 = BankAccount::factory()->create(['current_balance' => 5000]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'bank_account_id' => $bank1->id,
            'status' => 'pending',
            'extracted_amount' => 500,
            'extracted_direction' => 'debit',
        ]);

        $this->service->approveWithBankUpdate(
            $email,
            $this->operator->email,
            $bank2->id // Override bank account
        );

        $email->refresh();
        $bank1->refresh();
        $bank2->refresh();

        $this->assertEquals($bank2->id, $email->bank_account_id);
        $this->assertEquals(10000, $bank1->current_balance); // Unchanged
        $this->assertEquals(4500, $bank2->current_balance);  // Updated
    }

    public function test_approval_is_atomic_with_bank_update()
    {
        $this->bank->update(['current_balance' => 10000]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'bank_account_id' => $this->bank->id,
            'status' => 'pending',
            'extracted_amount' => 500,
            'extracted_direction' => 'debit',
        ]);

        $this->service->approveWithBankUpdate($email, $this->operator->email);

        $email->refresh();
        $this->bank->refresh();

        // Verify both operations completed
        $this->assertEquals('processed', $email->status);
        $this->assertTrue($email->applied_to_bank);
        $this->assertEquals(9500, $this->bank->current_balance);
    }

    public function test_rejection_clears_approval_flag()
    {
        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'status' => 'pending',
            'applied_to_bank' => false,
        ]);

        $this->service->reject($email, $this->operator->email);

        $email->refresh();
        $this->assertEquals('rejected', $email->status);
        $this->assertFalse($email->applied_to_bank);
    }

    public function test_approve_credit_adds_to_balance()
    {
        $initialBalance = 10000.0;
        $this->bank->update(['current_balance' => $initialBalance]);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'bank_account_id' => $this->bank->id,
            'status' => 'pending',
            'extracted_amount' => 500,
            'extracted_direction' => 'credit',
        ]);

        $this->service->approveWithBankUpdate($email, $this->operator->email);

        $this->bank->refresh();
        $this->assertEquals($initialBalance + 500, $this->bank->current_balance);
    }

    public function test_dispatch_email_processed_event_on_approval()
    {
        $this->expectsEvents(\App\Events\EmailProcessedEvent::class);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'status' => 'pending',
        ]);

        $this->service->approve($email, $this->operator->email);
    }

    public function test_dispatch_email_processed_event_on_rejection()
    {
        $this->expectsEvents(\App\Events\EmailProcessedEvent::class);

        $email = PendingEmail::factory()->create([
            'email_account_id' => $this->account->id,
            'status' => 'pending',
        ]);

        $this->service->reject($email, $this->operator->email);
    }
}
