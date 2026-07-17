<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Comportament captură
            $table->string('capture_mode')->default('unseen');   // all | unseen
            $table->boolean('move_to_hold')->default(true);
            $table->boolean('mark_as_read')->default(false);
            $table->boolean('auto_apply_rules')->default(true);
            $table->boolean('extract_bank_data')->default(true);

            // Randare / securitate
            $table->boolean('sanitize_html')->default(true);
            $table->string('attachments_disk')->default('local');

            // Implicite
            $table->string('default_currency', 3)->default('RON');
            $table->string('default_hold_folder')->default('INBOX.Hold');
            $table->unsignedSmallInteger('default_fetch_limit')->default(50);

            $table->timestamps();
        });

        // Rândul singleton de setări.
        DB::table('settings')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
