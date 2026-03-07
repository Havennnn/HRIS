<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PiaCore\Models\UploadedFile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(UploadedFile::class, 'sss_id')->nullable()->nullOnDelete();
            $table->foreignIdFor(UploadedFile::class, 'philhealth_id')->nullable()->nullOnDelete();
            $table->foreignIdFor(UploadedFile::class, 'bir_id')->nullable()->nullOnDelete();
            $table->foreignIdFor(UploadedFile::class, 'medical_id')->nullable()->nullOnDelete();
            $table->timestamps();

            $table->index('employee_id');
            $table->index('sss_id');
            $table->index('philhealth_id');
            $table->index('bir_id');
            $table->index('medical_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
