<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return view('admin.roles.index'); //

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validar que se cree bien
        $request->validate([
            'name' => 'required|unique:roles,name,NULL,id,guard_name,web'
        ]);

        //Si pasa la validación, creará el rol
        Role::create([
            'name' => $request->name,
            'guard_name' => 'web', // Valor por defecto para guard_name
        ]);

        //variale de un solo uso para alerta de SweetAlert2
        session()->flash('swal',
        [
            'icon' => 'success',
            'title' => 'Rol creado correctamente',
            'text' => 'El rol ha sido creado correctamente',
        ]);

        //Redireccionará a la tabla principal
        return redirect()->route('admin.roles.index')
        ->with('success', 'Rol created succesfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        return view('admin.roles.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar el rol
        $role = Role::findOrFail($id);
        
        // Eliminar el rol
        $role->delete();

        // Variable de un solo uso para alerta de SweetAlert2
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol eliminado correctamente',
            'text' => 'El rol ha sido eliminado correctamente',
        ]);

        // Redireccionar a la tabla principal
        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol eliminado correctamente');
    }
}
