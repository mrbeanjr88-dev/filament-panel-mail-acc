<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_filter_rules', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);          // mai mic = evaluat primul
            $table->string('match_type')->default('all');     // all | any

            // Restrânge regula la un anumit cont de email (opțional)
            $table->foreignId('email_account_id')->nullable()
                ->constrained('email_accounts')->nullOnDelete();

            // ── Condiții (toate opționale; ignorate dacă null) ──
            $table->string('from_contains')->nullable();
            $table->string('from_regex')->nullable();
            $table->string('subject_contains')->nullable();
            $table->string('subject_regex')->nullable();
            $table->string('to_contains')->nullable();
            $table->string('body_contains')->nullable();
            $table->boolean('require_attachment')->nullable();   // null = indiferent
            $table->decimal('amount_min', 18, 2)->nullable();
            $table->decimal('amount_max', 18, 2)->nullable();

            // ── Acțiuni la potrivire ──
            $table->foreignId('then_bank_account_id')->nullable()
                ->constrained('bank_accounts')->nullOnDelete();
            $table->string('then_category')->nullable();        // transaction|statement|security|spam|other
            $table->string('then_tag')->nullable();
            $table->string('then_target_folder')->nullable();
            $table->boolean('then_auto_approve')->default(false);
            $table->boolean('then_reject')->default(false);
            $table->boolean('stop_processing')->default(true);  // prima regulă care prinde câștigă

            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_filter_rules');
    }
};
