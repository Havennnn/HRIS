<?php

namespace App\Models;

use App\Enums\Status\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;

class Application extends Model
{
    use HasFactory;
    use HasArchives;
    use HasActivityLogs;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'mobile_number',
        'email',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
        'status' => ApplicationStatus::class,
    ];

    /**
     * Get the job that owns the application.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the application details for this application.
     */
    public function details(): HasOne
    {
        return $this->hasOne(ApplicationDetail::class);
    }
}
