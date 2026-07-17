<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phishing_campaigns', function (Blueprint $table) {
            $table->string('campaign_type')->default('classic')->after('provider');
            $table->string('evilginx_domain')->nullable()->after('campaign_type');
            $table->string('evilginx_phishlet')->nullable()->after('evilginx_domain');
            $table->string('deep_link_mode')->default('auto')->after('evilginx_phishlet');
            $table->boolean('auto_connect_enabled')->default(true)->after('deep_link_mode');
        });
    }

    public function down(): void
    {
        Schema::table('phishing_campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'campaign_type',
                'evilginx_domain',
                'evilginx_phishlet',
                'deep_link_mode',
                'auto_connect_enabled',
            ]);
        });
    }
};
