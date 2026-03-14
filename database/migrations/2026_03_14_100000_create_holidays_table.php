<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->string('type')->comment(HolidayType::class);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->archives();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
