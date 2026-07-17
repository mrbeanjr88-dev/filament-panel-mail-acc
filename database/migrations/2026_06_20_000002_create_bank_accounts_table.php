<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('label');                 // ex: „BT Cont curent RON”
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('iban')->nullable()->index();
            $table->string('bic')->nullable();
            $table->string('account_number')->nullable();
            $table->string('currency', 3)->default('RON');

            // Sold actualizat din mailuri
            $table->decimal('current_balance', 18, 2)->nullable();
            $table->decimal('available_balance', 18, 2)->nullable();
            $table->timestamp('balance_updated_at')->nullable();
            $table->timestamp('last_statement_at')->nullable();

            // Legătură opțională cu un cont de email care notifică pentru această bancă
            $table->foreignId('email_account_id')->nullable()
                ->constrained('email_accounts')->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
