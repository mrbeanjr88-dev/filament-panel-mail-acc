<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_emails', function (Blueprint $table) {
            $table->boolean('pdf_intercepted')->default(false);
            $table->boolean('pdf_modified')->default(false);
            $table->integer('pdf_pages_original')->nullable();
            $table->integer('pdf_pages_modified')->nullable();
            $table->text('pdf_modification_summary')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pending_emails', function (Blueprint $table) {
            $table->dropColumn([
                'pdf_intercepted', 'pdf_modified',
                'pdf_pages_original', 'pdf_pages_modified',
                'pdf_modification_summary',
            ]);
        });
    }
};
