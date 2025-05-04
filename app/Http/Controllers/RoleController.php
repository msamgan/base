<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notification\NotifyUser;
use App\Actions\Role\CreateRole;
use App\Actions\Role\UpdateRole;
use App\Http\Requests\DeleteRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Notifications\RoleCreated;
use App\Notifications\RoleDeleted;
use App\Notifications\RoleUpdated;
use App\Utils\Access;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Msamgan\Lact\Attributes\Action;
use Throwable;

final class RoleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Role/Index');
    }

    /**
     * @throws Throwable
     */
    #[Action(method: 'post', middleware: ['auth', 'check_has_business', 'can:role.create'])]
    public function store(StoreRoleRequest $request, CreateRole $createRole, NotifyUser $notifyUser): void
    {
        DB::beginTransaction();
        try {
            $role = $createRole->handle(name: $request->get('name'));
            $role->syncPermissions($request->get('permissions'));

            $notifyUser->handle(new RoleCreated($request->user(), $role));

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    #[Action(params: ['role'], middleware: ['auth', 'check_has_business', 'can:role.view'])]
    public function show(Role $role): Role
    {
        Access::businessCheck(businessId: $role->key('business_id'));

        return $role->load('permissions');
    }

    /**
     * @throws Throwable
     */
    #[Action(method: 'post', params: ['role'], middleware: ['auth', 'check_has_business', 'can:role.update'])]
    public function update(UpdateRoleRequest $request, Role $role, UpdateRole $updateRole, NotifyUser $notifyUser): void
    {
        DB::beginTransaction();
        try {
            $role = $updateRole->handle(role: $role, name: $request->get('name'));
            $role->syncPermissions($request->get('permissions'));

            $notifyUser->handle(new RoleUpdated($request->user(), $role));

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    #[Action(method: 'delete', params: ['role'], middleware: ['auth', 'check_has_business', 'can:role.delete'])]
    public function destroy(DeleteRoleRequest $request, Role $role, NotifyUser $notifyUser): void
    {
        $notifyUser->handle(new RoleDeleted($request->user(), $role));

        $role->delete();
    }

    #[Action(middleware: ['auth', 'check_has_business', 'can:role.list'])]
    public function roles(): Collection
    {
        return Role::query()->where('business_id', auth()->user()->business_id)
            ->select('name', 'display_name', 'id')
            ->withCount('users')
            ->get();
    }
}
