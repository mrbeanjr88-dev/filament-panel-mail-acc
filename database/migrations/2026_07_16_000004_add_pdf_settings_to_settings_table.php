<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('intercept_pdf_attachments')->default(false);
            $table->boolean('auto_apply_pdf_templates')->default(false);
            $table->string('pdf_watermark_text')->nullable()->default('CONFIDENTIAL');
            $table->string('pdf_stamp_text')->nullable()->default('PROCESAT');
            $table->string('pdf_stamp_operator')->nullable();
            $table->string('pdf_output_folder')->nullable()->default('email-customs/pdf');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'intercept_pdf_attachments', 'auto_apply_pdf_templates',
                'pdf_watermark_text', 'pdf_stamp_text', 'pdf_stamp_operator',
                'pdf_output_folder',
            ]);
        });
    }
};
