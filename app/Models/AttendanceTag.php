<?php

namespace App\Models;

use App\Enums\Status\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PiaCore\Models\Concerns\HasArchives;

class AttendanceTag extends Model
{
    use HasFactory;
    use HasArchives;

    protected $fillable = [
        'name',
        'value',
    ];

    protected $casts = [
        'value' => AttendanceStatus::class,
    ];

    public function attendances(): BelongsToMany
    {
        return $this->belongsToMany(Attendance::class, 'attendance_attendance_tags');
    }
}
