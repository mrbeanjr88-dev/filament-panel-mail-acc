<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_emails', function (Blueprint $table) {
            // Index for status + date filtering (common in dashboard)
            $table->index(['status', 'date']);

            // Index for account-scoped filtering
            $table->index(['email_account_id', 'status']);

            // Index for finding unapplied emails per bank account
            $table->index(['bank_account_id', 'applied_to_bank']);

            // Index for amount range filtering
            $table->index('extracted_amount');

            // Index for approved_at queries (dashboard today-processed count)
            $table->index('approved_at');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            // Index for FK lookups when syncing email account
            $table->index(['email_account_id', 'is_active']);
        });

        Schema::table('email_filter_rules', function (Blueprint $table) {
            // Extend existing composite with email_account_id
            $table->index(['email_account_id', 'is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('pending_emails', function (Blueprint $table) {
            $table->dropIndex(['status', 'date']);
            $table->dropIndex(['email_account_id', 'status']);
            $table->dropIndex(['bank_account_id', 'applied_to_bank']);
            $table->dropIndex(['extracted_amount']);
            $table->dropIndex(['approved_at']);
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropIndex(['email_account_id', 'is_active']);
        });

        Schema::table('email_filter_rules', function (Blueprint $table) {
            $table->dropIndex(['email_account_id', 'is_active', 'priority']);
        });
    }
};
