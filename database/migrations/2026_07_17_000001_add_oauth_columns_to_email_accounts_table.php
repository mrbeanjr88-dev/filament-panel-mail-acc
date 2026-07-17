<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_accounts', function (Blueprint $table) {
            $table->string('oauth_provider')->nullable()->after('authentication');
            $table->text('oauth_access_token')->nullable()->after('oauth_provider');
            $table->text('oauth_refresh_token')->nullable()->after('oauth_access_token');
            $table->timestamp('oauth_expires_at')->nullable()->after('oauth_refresh_token');
        });
    }

    public function down(): void
    {
        Schema::table('email_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'oauth_provider',
                'oauth_access_token',
                'oauth_refresh_token',
                'oauth_expires_at',
            ]);
        });
    }
};
