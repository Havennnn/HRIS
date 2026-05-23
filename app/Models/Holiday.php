<?php

namespace App\Models;

use App\Enums\Type\HolidayType;
use Illuminate\Database\Eloquent\Model;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;

class Holiday extends Model
{
    use HasActivityLogs;
    use HasArchives;

    protected $fillable = [
        'name',
        'date',
        'type',
        'is_paid',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'type' => HolidayType::class,
        'is_paid' => 'boolean',
    ];

    public function scopeRegular($query)
    {
        return $query->where('type', HolidayType::REGULAR->value);
    }

    public function scopeSpecial($query)
    {
        return $query->where('type', HolidayType::SPECIAL->value);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }
}
