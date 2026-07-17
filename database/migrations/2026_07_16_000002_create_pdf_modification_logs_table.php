<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_modification_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pending_email_id')->constrained('pending_emails')->cascadeOnDelete();
            $table->foreignId('pending_email_attachment_id')->nullable()
                ->constrained('pending_email_attachments')->nullOnDelete();
            $table->foreignId('pdf_template_id')->nullable()
                ->constrained('pdf_templates')->nullOnDelete();

            $table->string('operation'); // watermark|stamp|merge|redact|extract|rotate|flatten|generate
            $table->json('params')->nullable(); // parametrii operației
            $table->json('result')->nullable(); // {path, disk, size, pages}

            $table->string('status')->default('pending'); // pending|completed|failed
            $table->text('error')->nullable();

            $table->string('performed_by')->nullable(); // email operator

            $table->timestamps();

            $table->index(['pending_email_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_modification_logs');
    }
};
