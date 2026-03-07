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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'sss_id',
        'philhealth_id',
        'bir_id',
        'medical_id',
    ];

    /**
     * Get the employee that owns the document.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the SSS file.
     */
    public function sssFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'sss_id');
    }

    /**
     * Get the PhilHealth file.
     */
    public function philhealthFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'philhealth_id');
    }

    /**
     * Get the BIR file.
     */
    public function birFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'bir_id');
    }

    /**
     * Get the Medical file.
     */
    public function medicalFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class, 'medical_id');
    }
}
