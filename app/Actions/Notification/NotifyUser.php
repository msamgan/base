<?php

declare(strict_types=1);

namespace App\Actions\Notification;

final class NotifyUser
{
    public function handle($instance): void
    {
        auth()->user()->notify($instance);

        if (! auth()->user()->isBusiness()) {
            (new NotifyBusiness)->handle($instance);
        }
    }
}
