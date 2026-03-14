<?php

use App\Enums\Status\RequestStatus;
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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('status')->default(RequestStatus::PENDING->value);
            $table->text('message')->nullable();
            $table->date('requested_date');
            $table->date('end_date')->nullable();
            $table->integer('days')->nullable();
            $table->integer('overtime_hours')->nullable();
            $table->timestamps();
            $table->archives();

            $table->index('employee_id');
            $table->index('type');
            $table->index('status');
            $table->index('requested_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
