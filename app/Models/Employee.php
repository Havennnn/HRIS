<?php

namespace App\Models;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use PiaCore\Concerns\HasOptions;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;
use PiaCore\Search\SearchConfig;

class Employee extends Authenticatable
{
    use HasActivityLogs;
    use HasApiTokens;
    use HasArchives;
    use HasFactory;
    use HasOptions;
    use Notifiable;
    use SearchConfig;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'position_id',
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'hired_date',
        'mobile_number',
        'email',
        'status',
        'type',
        'password',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'hired_date' => 'date',
        'password_reset_expires_at' => 'datetime',
        'status' => EmployeeStatus::class,
        'type' => EmployeeType::class,
        'password' => 'hashed',
    ];

    /**
     * --------------------------------------------------------------------------
     * Search Configuration
     * --------------------------------------------------------------------------
     */
    protected array $searchable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'mobile_number',
        'position.name',
        'position.department.name',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contact(): HasOne
    {
        return $this->hasOne(EmployeeContact::class);
    }

    public function tools(): HasOne
    {
        return $this->hasOne(EmployeeTool::class);
    }

    public function device(): HasOne
    {
        return $this->hasOne(EmployeeDevice::class);
    }

    public function shift(): HasOne
    {
        return $this->hasOne(Shift::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    /**
     * --------------------------------------------------------------------------
     * Accessors
     * --------------------------------------------------------------------------
     */
    public function getFullNameAttribute(): string
    {
        $middleInitial = $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)).'.'
            : null;

        return trim("{$this->first_name} {$middleInitial} {$this->last_name}");
    }

    /**
     * Get the column name to use for the option label.
     */
    protected static function optionLabelColumn(): string
    {
        return 'first_name';
    }

    /**
     * Send a password reset link to the employee's email.
     * When mail is not configured, logs the link to the laravel.log file.
     */
    public function sendPasswordResetLink(): string
    {
        $token = $this->generatePasswordResetToken();

        $resetUrl = URL::temporarySignedRoute(
            'api.v1.auth.password.reset',
            now()->addMinutes(60),
            ['token' => $token]
        );

        $expiresAt = $this->password_reset_expires_at?->format('Y-m-d H:i:s');
        Log::info("[Employee Password Reset] Email: {$this->email} | Reset URL: {$resetUrl} | Expires: {$expiresAt}");

        return $token;
    }

    /**
     * Generate a password reset token for the employee.
     */
    public function generatePasswordResetToken(): string
    {
        $token = Str::random(64);

        $this->forceFill([
            'password_reset_token' => hash('sha256', $token),
            'password_reset_expires_at' => now()->addMinutes(60),
        ])->save();

        return $token;
    }

    /**
     * Verify a password reset token.
     */
    public function verifyPasswordResetToken(string $token): bool
    {
        if (empty($this->password_reset_token) || empty($this->password_reset_expires_at)) {
            return false;
        }

        return hash('sha256', $token) === $this->password_reset_token
            && now()->lt($this->password_reset_expires_at);
    }

    /**
     * Clear the password reset token.
     */
    public function clearPasswordResetToken(): void
    {
        $this->forceFill([
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ])->save();
    }
}
