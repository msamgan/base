<?php

declare(strict_types=1);

namespace App\Concerns;

trait PermissionFunctions
{
    public function makePermissionName(string $module, string $permission): string
    {
        return $module . '.' . $permission;
    }

    public function permissionList(): array
    {
        return [
            'list',
            'create',
            'view',
            'update',
            'delete',
        ];
    }
}
