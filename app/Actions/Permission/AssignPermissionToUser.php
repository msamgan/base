<?php

declare(strict_types=1);

namespace App\Actions\Permission;

use App\Concerns\PermissionFunctions;
use App\Models\User;
use Spatie\Permission\Models\Permission;

final class AssignPermissionToUser
{
    use PermissionFunctions;

    public function handle(User $user, string $permission, string $module): void
    {
        $permissionName = $this->makePermissionName(module: $module, permission: $permission);

        $permissionExists = Permission::query()->where('name', $permissionName)->first();
        if (! $permissionExists) {
            (new CreatePermission)->handle(permission: $permission, module: $module);
        }

        $user->givePermissionTo($permissionName);
    }
}
