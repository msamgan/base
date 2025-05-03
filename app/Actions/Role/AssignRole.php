<?php

declare(strict_types=1);

namespace App\Actions\Role;

use App\Models\Role;
use App\Models\User;

final class AssignRole
{
    public function handle(User $user, Role $role, bool $makeRoleActive = false): void
    {
        $user->roles()->detach();

        $user->assignRole($role);

        if ($makeRoleActive) {
            $user->saveKey('role_id', $role->getKey());
        }
    }
}
