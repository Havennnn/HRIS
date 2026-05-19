<?php

use App\Enums\Status\PerformanceReviewStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->decimal('overall_score', 5, 2)->nullable()->change();
            $table->unsignedTinyInteger('status')->default(PerformanceReviewStatus::PENDING->value)->comment(PerformanceReviewStatus::class)->change();
        });
    }

    public function down(): void
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->decimal('overall_score', 5, 2)->nullable(false)->change();
            $table->string('status')->default('pending')->change();
        });
    }
};
