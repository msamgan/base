<?php

declare(strict_types=1);

namespace App\Actions\Business;

use App\Enums\RoleEnum;
use App\Models\User;

final class BusinessUser
{
    public function handle(): User
    {
        return User::query()
            ->where('business_id', auth()->user()->business_id)
            ->whereHas('role', function ($query) {
                $query->where('display_name', RoleEnum::Business->value);
            })
            ->first();
    }
}
