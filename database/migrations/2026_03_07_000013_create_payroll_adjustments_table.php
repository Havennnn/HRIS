<?php

use App\Models\Payroll;
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
        Schema::create('payroll_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Payroll::class)->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('type');
            $table->string('reason');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->index('payroll_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_adjustments');
    }
};
