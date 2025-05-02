<?php

declare(strict_types=1);

namespace App\Actions\Business;

use App\Models\Business;

final class UpdateBusiness
{
    public function handle(Business $business, array $data): Business
    {
        $business->update($data);

        return $business;
    }
}
