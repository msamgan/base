<?php

declare(strict_types=1);

namespace App\Actions\Role;

use App\Models\Role;

final class UpdateRole
{
    public function handle(Role $role, string $name): \Spatie\Permission\Contracts\Role|\Spatie\Permission\Models\Role
    {
        $name = CreateRole::processRoleName($name);

        $role->update(['display_name' => $name]);

        return $role->refresh();
    }
}
