<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        $roles= Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Guarda un nuevo usuario (temporalmente vacío).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:users',
            'phone' => 'nullable|digits_between:7,15',
            'address' => 'nullable|string|min:3|max:255',
            'role_id'=>'required|exists:roles,id',
        ]);

        $user = User::create($data);

        // Asignar rol usando Spatie Permission
        $role = Role::findOrFail($data['role_id']);
        $user->assignRole($role);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado',
            'text' => 'El usuario ha sido creado exitosamente.',
            ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');


    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $user)
    {
        $roles= Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza un usuario existente (temporalmente vacío).
     */
    public function update(Request $request, User $user)
    {
        $validationRules = [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'id_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:users,id_number,' . $user->id,
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ];

        // Validar contraseña solo si se proporciona
        if ($request->filled('password')) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
        }

        $data = $request->validate($validationRules);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'id_number' => $data['id_number'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        // Actualizar contraseña solo si se proporcionó
        if ($request->filled('password')) {
            $user->password = $data['password']; // El modelo tiene cast 'hashed', se hasheará automáticamente
            $user->save();
        }

        // Actualizar rol usando Spatie Permission
        $role = Role::findOrFail($data['role_id']);
        $user->syncRoles([$role]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario actualizado',
            'text' => 'El usuario ha sido actualizado exitosamente.',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario de forma segura.
     */
    public function destroy(User $user)
    {
       
        // Prevenir que el admin se elimine a sí mismo
        if ($user->id === auth()->id()) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No puedes eliminar tu propia cuenta.',
            ]);
            abort(403, 'No puedes eliminarte a ti mismo.');
        }

        try {
            // Eliminar roles asociados al usuario usando Spatie Permission
            $user->roles()->detach();

            // Eliminar usuario (hard delete)
            $user->delete();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Usuario eliminado',
                'text' => 'El usuario ha sido eliminado exitosamente.',
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el usuario. Por favor, intenta nuevamente.',
            ]);

            return redirect()->route('admin.users.index')->with('error', 'No se pudo eliminar el usuario.');
        }
    }
}
