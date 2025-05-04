<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notification\NotifyUser;
use App\Actions\Role\AssignRole;
use App\Enums\RoleEnum;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\UserCreated;
use App\Notifications\UserDeleted;
use App\Notifications\UserUpdated;
use App\Utils\Access;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Msamgan\Lact\Attributes\Action;
use Throwable;

final class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('User/Index');
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    #[Action(method: 'post', middleware: ['auth', 'check_has_business', 'can:user.create'])]
    public function store(StoreUserRequest $request, AssignRole $assignRole, NotifyUser $notifyUser): void
    {
        DB::beginTransaction();

        try {
            $user = User::query()->create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password')),
                'business_id' => auth()->user()->business_id,
            ]);

            $role = Role::query()->find($request->get('role'));

            $assignRole->handle(user: $user, role: $role, makeRoleActive: true);

            $notifyUser->handle(new UserCreated(auth()->user(), $user, $role));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    #[Action(params: ['user'], middleware: ['auth', 'check_has_business', 'can:user.view'])]
    public function show(User $user): User
    {
        Access::businessCheck(businessId: $user->business_id);

        return $user->load('role');
    }

    #[Action(method: 'post', params: ['user'], middleware: ['auth', 'check_has_business', 'can:user.update'])]
    public function update(UpdateUserRequest $request, User $user, AssignRole $assignRole, NotifyUser $notifyUser): void
    {
        if ($request->get('password')) {
            $request->merge(['password' => bcrypt($request->get('password'))]);
        }

        $role = Role::query()->find($request->get('role'));

        $assignRole->handle(user: $user, role: $role, makeRoleActive: true);

        $filteredData = array_filter(
            array_diff_key($request->validated(), ['role' => '']),
            fn ($value): bool => ! is_null($value)
        );

        $user->update($filteredData);

        $notifyUser->handle(new UserUpdated(auth()->user(), $user));
    }

    #[Action(method: 'delete', params: ['user'], middleware: ['auth', 'check_has_business', 'can:user.delete'])]
    public function destroy(DeleteUserRequest $request, User $user, NotifyUser $notifyUser): void
    {
        $notifyUser->handle(new UserDeleted(auth()->user(), $user));

        $user->delete();
    }

    #[Action(middleware: ['auth', 'check_has_business', 'can:user.list'])]
    public function users(): Collection
    {
        $query = User::query()->where('business_id', auth()->user()->key('business_id'))
            ->where('id', '!=', auth()->id())
            ->with(['roles']);

        if (! auth()->user()->hasRole([RoleEnum::Business, RoleEnum::SuperAdmin])) {
            $query->whereDoesntHave('roles', function ($q): void {
                $q->where('display_name', RoleEnum::Business);
            });
        }

        return $query->get();
    }
}
