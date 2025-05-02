<?php

declare(strict_types=1);

namespace App\Actions;

final class Access
{
    public static function businessCheck(?int $businessId): bool
    {
        abort_if(auth()->user()->business_id !== $businessId, 403, 'You do not have access');

        return true;
    }
}
