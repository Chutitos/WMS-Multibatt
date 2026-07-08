<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (! Auth::user()->can('changeRole', [$user, (int) $validated['role_id']])) {
            return redirect()->back()->withInput()
                ->with('error', 'No puedes quitarle el rol de administrador al último admin activo del sistema.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado.');
    }
    public function toggleActivo(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes cambiar el estado de tu propio usuario.');
        }

        if (! Auth::user()->can('toggleActivo', $user)) {
            return redirect()->back()->with('error', 'No puedes desactivar al último administrador activo del sistema.');
        }

        $user->update(['activo' => ! $user->activo]);

        return redirect()->route('users.index')
            ->with('success', $user->activo ? 'Usuario activado.' : 'Usuario desactivado.');
    }
}
