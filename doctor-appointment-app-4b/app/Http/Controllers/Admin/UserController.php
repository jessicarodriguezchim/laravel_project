<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** Listado de usuarios */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /** Formulario de creación */
    public function create()
    {
        return view('admin.users.create');
    }

    /** Guardar nuevo usuario */
    public function store(Request $request)
    {
        // Valida igual que hagas con Roles, pero adaptado a User
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // agrega lo que uses (password, role_id, etc.)
        ]);

        // Ojo con password si lo usas:
        // $data['password'] = bcrypt($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /** Formulario de edición */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /** Actualizar usuario */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /** Eliminar usuario */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
