<?php

namespace App\Models;

use App\Enums\Status\CareerStatus;
use PiaCore\Models\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;
use PiaCore\Search\SearchConfig;

class Career extends Model
{
    use HasActivityLogs;
    use HasArchives;
    use HasFactory;
    use HasSeoMeta;
    use SearchConfig;

    protected $fillable = [
        'position_id',
        'description',
        'salary',
        'status',
    ];

    protected array $searchable = [
        'description',
        'position.name',
    ];

    protected $casts = [
        'status' => CareerStatus::class,
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
