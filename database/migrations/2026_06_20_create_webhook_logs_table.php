<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pending_email_id')->constrained()->cascadeOnDelete();
            $table->string('event_type'); // 'approved', 'rejected', 'failed'
            $table->string('webhook_url');
            $table->integer('status_code')->nullable();
            $table->text('request_payload')->nullable();
            $table->text('response')->nullable();
            $table->boolean('success')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['pending_email_id', 'event_type']);
            $table->index(['success', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
