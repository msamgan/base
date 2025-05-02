<?php

declare(strict_types=1);

namespace App\Actions;

final class Access
{
    public static function businessCheck(?int $businessId): bool
    {
        if (auth()->user()->business_id !== $businessId) {
            abort(403, 'You do not have access');
        }

        return true;
    }
}
