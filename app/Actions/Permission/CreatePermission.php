<?php

declare(strict_types=1);

namespace App\Actions\Permission;

use App\Concerns\PermissionFunctions;
use App\Enums\RoleEnum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class CreatePermission
{
    use PermissionFunctions;

    public function handle(string $permission, string $module): void
    {
        Permission::create(['name' => $this->makePermissionName(module: $module, permission: $permission)]);

        // when ever you create a new permission, you assign it to super admin Role.
        (new AssignPermissionToRole)->handle(
            role: Role::query()->where('display_name', RoleEnum::SuperAdmin->value)->first(),
            permission: $permission,
            module: $module
        );
    }
}
