<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\HigherOrderCollectionProxy;
use Msamgan\Lact\Attributes\Action;
use Spatie\Permission\Models\Permission;

final class PermissionController extends Controller
{
    private array $excludedModules = ['business'];

    #[Action(middleware: ['auth'])]
    public function permissions(): Collection|HigherOrderCollectionProxy
    {
        $filteredPermissions = [];
        Permission::query()->get()->each(function ($permission) use (&$filteredPermissions): void {
            [$module, $action] = explode('.', (string) $permission->name);

            if (in_array($module, $this->excludedModules)) {
                return;
            }

            $filteredPermissions[] = [
                'module' => $module,
                'name' => $action,
                'id' => $permission->id,
            ];
        });

        return collect($filteredPermissions)->groupBy('module');
    }
}
