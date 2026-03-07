<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PiaCore\Models\Concerns\HasUploadedFiles;
use PiaCore\Models\UploadedFile;

class ApplicationDetail extends Model
{
    use HasFactory;
    use HasUploadedFiles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'application_id',
        'resume_id',
        'portfolio_id',
    ];

    /**
     * Get the application that owns the details.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the resume file.
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'resume_id');
    }

    /**
     * Get the portfolio file.
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'portfolio_id');
    }
}
