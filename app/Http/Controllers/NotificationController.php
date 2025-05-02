<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

final class NotificationController extends Controller
{
    public function index(): Response
    {
        auth()->user()->unreadNotifications->markAsRead();

        return Inertia::render('Notifications');
    }
}
