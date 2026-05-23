<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            // Drop unused columns
            $table->dropColumn(['robots', 'canonical_url', 'og_type', 'twitter_card']);
        });

        Schema::table('seo_meta', function (Blueprint $table) {
            // Add keywords column
            $table->string('keywords')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });

        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('robots')->default('index,follow')->after('meta_description');
            $table->string('canonical_url')->nullable()->after('robots');
            $table->string('og_type')->default('website');
            $table->string('twitter_card')->default('summary_large_image');
        });
    }
};
