<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::with(['user', 'speciality'])->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialities = Speciality::all();
        // Usuarios que no tienen un registro de doctor aún y tienen rol Doctor
        $users = User::whereDoesntHave('doctor')->role('Doctor')->get();
        return view('admin.doctors.create', compact('specialities', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:doctors,user_id',
            'speciality_id' => 'nullable|exists:specialities,id',
            'license_number' => 'nullable|string|max:255',
            'biography' => 'nullable|string|max:1000',
        ]);

        Doctor::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Doctor creado',
            'text' => 'El doctor ha sido creado exitosamente.',
        ]);

        return redirect()->route('admin.doctors.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'speciality']);
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $specialities = Speciality::all();
        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        try {
            $data = $request->validate([
                'speciality_id' => 'nullable|exists:specialities,id',
                'license_number' => 'nullable|string|max:255',
                'biography' => 'nullable|string|max:1000',
            ]);

            $doctor->update($data);

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Doctor actualizado',
                'text' => 'El doctor ha sido actualizado exitosamente.',
            ]);

            return redirect()->route('admin.doctors.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('flash.banner', 'Error inesperado: No se pudo guardar la información.')
                ->with('flash.bannerStyle', 'danger');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        try {
            $doctor->delete();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Doctor eliminado',
                'text' => 'El doctor ha sido eliminado exitosamente.',
            ]);

            return redirect()->route('admin.doctors.index');
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el doctor. Por favor, intenta nuevamente.',
            ]);

            return redirect()->route('admin.doctors.index');
        }
    }
}

