<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');

            // Conexiune IMAP
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(993);
            $table->string('encryption')->default('ssl'); // ssl | tls | starttls | none
            $table->boolean('validate_cert')->default(true);
            $table->string('protocol')->default('imap');
            $table->string('username');
            $table->text('password');                      // cast 'encrypted'
            $table->string('authentication')->nullable();  // null | 'oauth'

            // Foldere
            $table->string('inbox_folder')->default('INBOX');
            $table->string('hold_folder')->default('INBOX.Hold');
            $table->string('approved_folder')->default('INBOX');
            $table->string('rejected_folder')->nullable();

            // Sync
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_sync')->default(true);
            $table->unsignedSmallInteger('fetch_limit')->default(50);
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_accounts');
    }
};
