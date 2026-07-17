<?php

namespace Tests\Unit\Services;

use App\Models\BankAccount;
use App\Models\EmailAccount;
use App\Models\PendingEmail;
use App\Services\ReportingService;
use Tests\TestCase;

class ReportingServiceTest extends TestCase
{
    protected ReportingService $service;
    protected EmailAccount $account;
    protected BankAccount $bank;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReportingService::class);
        $this->account = EmailAccount::factory()->create();
        $this->bank = BankAccount::factory()->create(['email_account_id' => $this->account->id]);
    }

    public function test_get_summary_counts()
    {
        PendingEmail::factory(5)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now(),
        ]);

        PendingEmail::factory(3)->create([
            'email_account_id' => $this->account->id,
            'status' => 'pending',
        ]);

        PendingEmail::factory(2)->create([
            'email_account_id' => $this->account->id,
            'status' => 'rejected',
        ]);

        $summary = $this->service->getSummary();

        $this->assertEquals(5, $summary['total_processed']);
        $this->assertEquals(3, $summary['total_pending']);
        $this->assertEquals(2, $summary['total_rejected']);
    }

    public function test_get_daily_processing_stats()
    {
        $today = now();

        PendingEmail::factory(3)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => $today,
            'extracted_amount' => 100,
        ]);

        PendingEmail::factory(1)->create([
            'email_account_id' => $this->account->id,
            'status' => 'rejected',
            'rejected_at' => $today,
        ]);

        $stats = $this->service->getDailyProcessingStats($today);

        $this->assertEquals(3, $stats['approved_count']);
        $this->assertEquals(1, $stats['rejected_count']);
        $this->assertEquals(300.0, $stats['total_amount_processed']);
    }

    public function test_get_monthly_trends()
    {
        // Create emails for last 3 months
        for ($i = 0; $i < 3; $i++) {
            $month = now()->subMonths($i);
            PendingEmail::factory(5)->create([
                'email_account_id' => $this->account->id,
                'status' => 'processed',
                'approved_at' => $month,
                'extracted_amount' => 100 + ($i * 50),
            ]);
        }

        $trends = $this->service->getMonthlyTrends(3);

        $this->assertEquals(3, $trends->count());
        $this->assertTrue($trends->every(fn ($t) => isset($t['month']) && isset($t['approved']) && isset($t['total'])));
    }

    public function test_get_operator_stats()
    {
        PendingEmail::factory(5)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now()->subDays(10),
            'approved_by' => 'operator1@example.com',
            'extracted_amount' => 100,
        ]);

        PendingEmail::factory(3)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now()->subDays(10),
            'approved_by' => 'operator2@example.com',
            'extracted_amount' => 50,
        ]);

        $stats = $this->service->getOperatorStats(30);

        $this->assertEquals(2, $stats->count());
        $this->assertEquals('operator1@example.com', $stats->first()['operator']);
        $this->assertEquals(5, $stats->first()['approved_count']);
    }

    public function test_get_bank_account_stats()
    {
        $bank1 = BankAccount::factory()->create();
        $bank2 = BankAccount::factory()->create();

        PendingEmail::factory(3)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now()->subDays(10),
            'bank_account_id' => $bank1->id,
            'extracted_amount' => 100,
        ]);

        PendingEmail::factory(2)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now()->subDays(10),
            'bank_account_id' => $bank2->id,
            'extracted_amount' => 50,
        ]);

        $stats = $this->service->getBankAccountStats(30);

        $this->assertEquals(2, $stats->count());
        $this->assertEquals(3, $stats->first()['transaction_count']);
    }

    public function test_get_top_categories()
    {
        PendingEmail::factory(5)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'category' => 'transaction',
        ]);

        PendingEmail::factory(3)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'category' => 'statement',
        ]);

        PendingEmail::factory(2)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'category' => 'security',
        ]);

        $categories = $this->service->getTopCategories(5);

        $this->assertEquals(3, $categories->count());
        $this->assertEquals(5, $categories->first()['count']);
    }

    public function test_summary_today_count()
    {
        PendingEmail::factory(2)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now(),
        ]);

        PendingEmail::factory(3)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now()->subDays(1),
        ]);

        $summary = $this->service->getSummary();

        $this->assertEquals(2, $summary['today_approved']);
    }

    public function test_summary_this_month_totals()
    {
        PendingEmail::factory(4)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now(),
            'extracted_amount' => 100,
        ]);

        PendingEmail::factory(2)->create([
            'email_account_id' => $this->account->id,
            'status' => 'processed',
            'approved_at' => now()->subMonths(1),
            'extracted_amount' => 100,
        ]);

        $summary = $this->service->getSummary();

        $this->assertEquals(4, $summary['this_month_count']);
        $this->assertEquals(400, $summary['this_month_amount']);
    }
}
