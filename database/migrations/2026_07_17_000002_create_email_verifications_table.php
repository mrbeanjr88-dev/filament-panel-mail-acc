<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->index();
            $table->string('email')->index();
            $table->string('domain')->index();

            // Provider detection
            $table->string('provider')->index();          // google, microsoft, yahoo, unknown, etc.
            $table->string('provider_label');              // Google Workspace / Gmail
            $table->float('confidence')->default(0);       // 0.0 - 1.0
            $table->string('imap_host')->nullable();
            $table->string('smtp_host')->nullable();
            $table->unsignedSmallInteger('port')->nullable();

            // MX records (JSON array)
            $table->json('mx_records')->nullable();

            // SMTP verification (optional)
            $table->boolean('smtp_checked')->default(false);
            $table->boolean('smtp_reachable')->nullable();
            $table->unsignedSmallInteger('smtp_response_code')->nullable();
            $table->text('smtp_error')->nullable();
            $table->string('smtp_mx_host')->nullable();

            // Status
            $table->string('status')->default('pending');  // pending, verified, failed
            $table->text('error')->nullable();
            $table->unsignedInteger('verification_time_ms')->nullable();

            // Metadata
            $table->string('source')->nullable();           // csv import, manual, api
            $table->text('raw_line')->nullable();           // original CSV line for debugging

            $table->timestamps();

            // Composite indexes for common queries
            $table->index(['batch_id', 'provider']);
            $table->index(['batch_id', 'status']);
            $table->index(['provider', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verifications');
    }
};
