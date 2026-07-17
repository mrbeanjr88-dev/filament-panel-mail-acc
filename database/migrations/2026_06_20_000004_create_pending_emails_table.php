<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_emails', function (Blueprint $table) {
            $table->id();

            $table->foreignId('email_account_id')->constrained('email_accounts')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()
                ->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('matched_rule_id')->nullable()
                ->constrained('email_filter_rules')->nullOnDelete();

            // Identificare server
            $table->string('message_id')->nullable();
            $table->unsignedInteger('hold_uid')->nullable();

            // Antet
            $table->string('subject')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->json('to')->nullable();
            $table->json('cc')->nullable();
            $table->timestamp('date')->nullable();

            // Conținut
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('raw_headers')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->boolean('has_attachments')->default(false);

            // Clasificare + date extrase pentru actualizare bancară
            $table->string('category')->nullable();          // transaction|statement|security|spam|other
            $table->string('tag')->nullable();
            $table->decimal('extracted_amount', 18, 2)->nullable();
            $table->string('extracted_currency', 3)->nullable();
            $table->decimal('extracted_balance', 18, 2)->nullable();
            $table->string('extracted_direction')->nullable(); // credit | debit
            $table->boolean('applied_to_bank')->default(false);

            // Flux
            $table->string('status')->default('pending');    // pending|approved|processed|rejected|failed
            $table->string('target_folder')->nullable();
            $table->boolean('modified')->default(false);
            $table->text('notes')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            $table->unique(['email_account_id', 'message_id']);
            $table->index('status');
            $table->index('category');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_emails');
    }
};
