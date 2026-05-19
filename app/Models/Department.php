<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Concerns\HasOptions;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;

class Department extends Model
{
    use HasActivityLogs;
    use HasArchives;
    use HasFactory;
    use HasOptions;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'name',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}
