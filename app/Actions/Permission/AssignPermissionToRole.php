<?php

declare(strict_types=1);

namespace App\Actions\Permission;

use App\Concerns\PermissionFunctions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class AssignPermissionToRole
{
    use PermissionFunctions;

    public function handle(Role $role, string $permission, string $module): void
    {
        $permissionName = $this->makePermissionName(module: $module, permission: $permission);

        $permissionExists = Permission::query()->where('name', $permissionName)->exists();
        if (! $permissionExists) {
            (new CreatePermission)->handle(permission: $permission, module: $module);
        }

        $role->givePermissionTo($permissionName);
    }
}
