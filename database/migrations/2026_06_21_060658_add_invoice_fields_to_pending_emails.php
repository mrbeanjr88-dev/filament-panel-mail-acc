<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add invoice columns (idempotent)
        Schema::table('pending_emails', function (Blueprint $table) {
            if (! Schema::hasColumn('pending_emails', 'invoice_number')) {
                $table->string('invoice_number', 100)->nullable()->after('extracted_direction');
            }
            if (! Schema::hasColumn('pending_emails', 'invoice_date')) {
                $table->date('invoice_date')->nullable()->after('invoice_number');
            }
            if (! Schema::hasColumn('pending_emails', 'due_date')) {
                $table->date('due_date')->nullable()->after('invoice_date');
            }
            if (! Schema::hasColumn('pending_emails', 'invoice_issuer')) {
                $table->string('invoice_issuer', 255)->nullable()->after('due_date');
            }
            if (! Schema::hasColumn('pending_emails', 'vat_amount')) {
                $table->decimal('vat_amount', 18, 2)->nullable()->after('invoice_issuer');
            }
        });

        // Add indexes for new columns
        try {
            Schema::table('pending_emails', function (Blueprint $table) {
                $table->index('invoice_number');
            });
        } catch (\Throwable) {}
        try {
            Schema::table('pending_emails', function (Blueprint $table) {
                $table->index('due_date');
            });
        } catch (\Throwable) {}

        // Drop composite index referencing applied_to_bank before dropping the column
        try {
            Schema::table('pending_emails', function (Blueprint $table) {
                $table->dropIndex('pending_emails_bank_account_id_applied_to_bank_index');
            });
        } catch (\Throwable) {}
        try {
            Schema::table('pending_emails', function (Blueprint $table) {
                $table->dropIndex(['bank_account_id', 'applied_to_bank']);
            });
        } catch (\Throwable) {}

        // Drop obsolete balance columns
        Schema::table('pending_emails', function (Blueprint $table) {
            $drop = array_filter(
                ['extracted_balance', 'applied_to_bank'],
                fn ($c) => Schema::hasColumn('pending_emails', $c)
            );
            if ($drop) {
                $table->dropColumn(array_values($drop));
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_emails', function (Blueprint $table) {
            $table->dropIndex(['invoice_number']);
            $table->dropIndex(['due_date']);
            $table->dropColumn(['invoice_number', 'invoice_date', 'due_date', 'invoice_issuer', 'vat_amount']);
            $table->decimal('extracted_balance', 18, 2)->nullable();
            $table->boolean('applied_to_bank')->default(false);
        });
    }
};
