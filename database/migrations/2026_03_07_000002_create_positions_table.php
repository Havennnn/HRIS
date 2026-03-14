<?php

use App\Models\Department;
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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Department::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('level')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->decimal('allowance', 10, 2)->nullable();
            $table->timestamps();
            $table->archives();

            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
