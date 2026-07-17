<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            foreach (['current_balance', 'available_balance', 'balance_updated_at', 'last_statement_at'] as $col) {
                if (Schema::hasColumn('bank_accounts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->decimal('current_balance', 18, 2)->nullable();
            $table->decimal('available_balance', 18, 2)->nullable();
            $table->timestamp('balance_updated_at')->nullable();
            $table->timestamp('last_statement_at')->nullable();
        });
    }
};
