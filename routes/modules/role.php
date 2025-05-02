<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check_has_business'])->group(function (): void {
    Route::get('roles', [RoleController::class, 'index'])
        ->middleware([PermissionEnum::RoleList->can()])
        ->name('role.index');
});
