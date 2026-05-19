<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PiaCore\Models\Concerns\HasUploadedFiles;
use PiaCore\Models\UploadedFile;

class EmployeeDocument extends Model
{
    use HasFactory;
    use HasUploadedFiles;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'employee_id',
        'sss_id',
        'philhealth_id',
        'bir_id',
        'medical_id',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function sssFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'sss_id');
    }

    public function philhealthFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'philhealth_id');
    }

    public function birFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'bir_id');
    }

    public function medicalFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'medical_id');
    }
}
