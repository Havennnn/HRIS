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
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    
    protected $fillable = [
        'application_id',
        'resume_id',
        'portfolio_id',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'resume_id');
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'portfolio_id');
    }
}
