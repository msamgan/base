<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $superAdmin = User::query()->create([
            'name' => 'Super Admin',
            'email' => 'sadmin@base.com',
            'password' => bcrypt('Pass@123!321'),
            'email_verified_at' => now(),
            'role_id' => RoleEnum::SuperAdmin->id(),
        ]);

        $superAdmin->assignRole(Role::superAdmin());
    }

    public function down(): void
    {
        //
    }
};
