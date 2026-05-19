<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->tinyInteger('period_start_day');
            $table->tinyInteger('period_end_day')->nullable();
            $table->boolean('period_end_is_last_day')->default(false);
            $table->tinyInteger('cutoff_generation_day');
            $table->tinyInteger('cutoff_disburse_day')->nullable();
            $table->boolean('disburse_is_last_day')->default(false);
            $table->tinyInteger('assumed_from_day');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_configurations');
    }
};
