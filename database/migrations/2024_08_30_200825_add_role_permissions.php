<?php

declare(strict_types=1);

use App\Actions\Permission\AssignPermissionToRole;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use App\Concerns\PermissionFunctions;

    public function up(): void
    {
        $businessRole = Role::business();

        foreach ($this->permissionList() as $permission) {
            (new AssignPermissionToRole)->handle(
                role: $businessRole,
                permission: $permission,
                module: 'role'
            );
        }

        foreach ($this->permissionList() as $permission) {
            (new AssignPermissionToRole)->handle(
                role: $businessRole,
                permission: $permission,
                module: 'user'
            );
        }
    }

    public function down(): void
    {
        //
    }
};
