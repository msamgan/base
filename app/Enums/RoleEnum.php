<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Role;

enum RoleEnum: string
{
    case SuperAdmin = 'SuperAdmin';
    case Business = 'Business';
    case Customer = 'Customer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'super_admin',
            self::Business => 'business',
            self::Customer => 'customer',
        };
    }

    public function id(): int
    {
        return match ($this) {
            self::SuperAdmin => 1,
            self::Business => 2,
            self::Customer => 3,
        };
    }

    public function role(): Role
    {
        return Role::query()
            ->whereNull('business_id')
            ->where('display_name', $this->value)->first();
    }
}
