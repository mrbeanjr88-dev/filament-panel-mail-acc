<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_email_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pending_email_id')->constrained('pending_emails')->cascadeOnDelete();

            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);

            $table->string('disk')->default('local');
            $table->string('path');

            $table->string('content_id')->nullable();
            $table->boolean('is_inline')->default(false);

            $table->boolean('is_removed')->default(false);
            $table->boolean('is_replaced')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_email_attachments');
    }
};
