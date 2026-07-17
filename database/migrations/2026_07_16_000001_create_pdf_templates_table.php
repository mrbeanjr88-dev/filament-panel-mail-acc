<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_templates', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type'); // watermark|stamp|header_footer|merge|generate|custom
            $table->boolean('is_active')->default(true);

            // Configurația operației (JSON cu parametrii)
            $table->json('config');

            // Opțional: legătură la cont email (null = toate conturile)
            $table->foreignId('email_account_id')->nullable()
                ->constrained('email_accounts')->nullOnDelete();

            // Auto-aplicare: dacă e activ, template-ul se aplică automat la sincronizare
            $table->boolean('auto_apply')->default(false);
            $table->integer('priority')->default(0);

            // Criterii de potrivire (JSON)
            $table->json('match_criteria')->nullable();
            // Ex: {"from_contains": "@firma.ro", "subject_contains": "factura", "require_pdf": true}

            $table->timestamps();

            $table->index(['is_active', 'auto_apply', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_templates');
    }
};
