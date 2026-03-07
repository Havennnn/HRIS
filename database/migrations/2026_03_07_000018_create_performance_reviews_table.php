<?php

use App\Models\Employee;
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
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class, 'employee_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(Employee::class, 'reviewer_id')->constrained('employees')->onDelete('cascade');
            $table->date('review_date');
            $table->decimal('overall_score', 5, 2);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('employee_id');
            $table->index('reviewer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
