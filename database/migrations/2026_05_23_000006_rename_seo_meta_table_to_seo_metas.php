<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('seo_meta', 'seo_metas');
    }

    public function down(): void
    {
        Schema::rename('seo_metas', 'seo_meta');
    }
};
