<?php

use App\Models\Kpi;
use App\Models\PerformanceReview;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('review_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PerformanceReview::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Kpi::class)->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->timestamps();

            $table->index('performance_review_id');
            $table->index('kpi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_scores');
    }
};
