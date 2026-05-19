<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Search\SearchConfig;

class Kpi extends Model
{
    use HasActivityLogs;
    use HasFactory;
    use SearchConfig;

    protected $fillable = [
        'name',
        'description',
    ];

    protected array $searchable = [
        'name',
        'description',
    ];

    public function reviewScores(): HasMany
    {
        return $this->hasMany(ReviewScore::class);
    }
}
