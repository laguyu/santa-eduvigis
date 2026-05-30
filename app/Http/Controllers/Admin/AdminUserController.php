<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->orderByDesc('is_admin')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => $data['is_admin'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(int $id): View
    {
        return view('admin.users.edit', [
            'adminUser' => User::query()->findOrFail($id),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, int $id): RedirectResponse
    {
        $adminUser = User::query()->findOrFail($id);
        $data = $request->validated();

        $isAdmin = (bool) ($data['is_admin'] ?? false);

        if ($this->mustKeepCurrentUserAdmin($request->user()->id, $adminUser->id, $isAdmin)) {
            return back()->withErrors([
                'is_admin' => 'No puedes quitar tu propio acceso al panel.',
            ])->withInput();
        }

        if ($this->wouldRemoveLastAdmin($adminUser, $isAdmin)) {
            return back()->withErrors([
                'is_admin' => 'Debe existir al menos un usuario con acceso admin al panel.',
            ])->withInput();
        }

        $adminUser->name = $data['name'];
        $adminUser->email = $data['email'];
        $adminUser->is_admin = $isAdmin;

        if (! empty($data['password'])) {
            $adminUser->password = Hash::make($data['password']);
        }

        $adminUser->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleAccess(int $id): RedirectResponse
    {
        $adminUser = User::query()->findOrFail($id);
        $newValue = ! $adminUser->is_admin;

        if (Auth::id() === $adminUser->id && ! $newValue) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'No puedes quitar tu propio acceso al panel.');
        }

        if ($this->wouldRemoveLastAdmin($adminUser, $newValue)) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'Debe existir al menos un usuario con acceso admin al panel.');
        }

        $adminUser->update(['is_admin' => $newValue]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', $newValue
                ? 'Acceso admin habilitado.'
                : 'Acceso admin deshabilitado.');
    }

    private function mustKeepCurrentUserAdmin(int $currentUserId, int $editedUserId, bool $newIsAdmin): bool
    {
        return $currentUserId === $editedUserId && ! $newIsAdmin;
    }

    private function wouldRemoveLastAdmin(User $adminUser, bool $newIsAdmin): bool
    {
        return $adminUser->is_admin && ! $newIsAdmin && User::query()->where('is_admin', true)->count() <= 1;
    }
}
