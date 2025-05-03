<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check_has_business'])->group(function (): void {
    Route::get('users', [UserController::class, 'index'])
        ->middleware([PermissionEnum::UserList->can()])
        ->name('user.index');
});
