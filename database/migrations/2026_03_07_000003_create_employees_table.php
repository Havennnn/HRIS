<?php

use App\Enums\Status\EmployeeStatus;
use App\Models\Position;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Position::class)->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birthdate');
            $table->string('mobile_number');
            $table->string('email');
            $table->string('password')->nullable();
            $table->date('hired_date')->nullable();
            $table->unsignedTinyInteger('status')->default(EmployeeStatus::ACTIVE);
            $table->unsignedTinyInteger('type');
            $table->timestamps();
            $table->archives();

            $table->index('position_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
