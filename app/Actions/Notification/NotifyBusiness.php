<?php

declare(strict_types=1);

namespace App\Actions\Notification;

use App\Actions\Business\BusinessUser;

final class NotifyBusiness
{
    public function handle($instance): void
    {
        (new BusinessUser)->handle()->notify($instance);
    }
}
