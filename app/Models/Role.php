<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\ModelFunctions;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @method static find(mixed $get)
 */
final class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;
    use LogsActivity;
    use ModelFunctions;

    protected $fillable = ['name', 'display_name', 'guard_name', 'business_id', 'created_by'];

    protected $hidden = [
        'guard_name',
        'created_at',
        'updated_at',
        'created_by',
    ];

    public static function business(): self
    {
        return self::query()->find(RoleEnum::Business->id());
    }

    public static function superAdmin(): self
    {
        return self::query()->find(RoleEnum::SuperAdmin->id());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable();
    }
}
