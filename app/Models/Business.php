<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\ModelFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @method static create(array $array)
 */
final class Business extends Model
{
    use HasFactory;
    use LogsActivity;
    use ModelFunctions;

    public $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'country',
        'city',
        'state',
        'zip',
        'timezone',
        'unit_system',
        'weight_unit',
        'currency',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
