<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\ModelFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 */
final class Menu extends Model
{
    use HasFactory;
    use ModelFunctions;

    protected $fillable = [
        'parent_id',
        'label',
        'route',
        'icon',
        'permission',
        'order',
        'is_active',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
