<?php

use App\Models\Application;
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
        Schema::create('application_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(UploadedFile::class, 'resume_id')->nullable()->nullOnDelete();
            $table->foreignIdFor(UploadedFile::class, 'portfolio_id')->nullable()->nullOnDelete();

            $table->index('application_id');
            $table->index('resume_id');
            $table->index('portfolio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_details');
    }
};
