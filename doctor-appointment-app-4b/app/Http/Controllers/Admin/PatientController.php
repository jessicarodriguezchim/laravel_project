<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::with(['user', 'bloodType'])->get();
        return view('admin.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bloodTypes = BloodType::all();
        // Usuarios que no tienen un registro de paciente aún
        $users = User::whereDoesntHave('patient')->role('Paciente')->get();
        return view('admin.patients.create', compact('bloodTypes', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:patients,user_id',
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|max:255',
            'chronic_conditions' => 'nullable|string|max:255',
            'surgical_history' => 'nullable|string|max:255',
            'family_history' => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
        ]);

        Patient::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Paciente creado',
            'text' => 'El paciente ha sido creado exitosamente.',
        ]);

        return redirect()->route('admin.patients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load(['user', 'bloodType']);
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $bloodTypes = BloodType::all();
        return view('admin.patients.edit', compact('patient', 'bloodTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|max:255',
            'chronic_conditions' => 'nullable|string|max:255',
            'surgical_history' => 'nullable|string|max:255',
            'family_history' => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
        ]);

        $patient->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Paciente actualizado',
            'text' => 'El paciente ha sido actualizado exitosamente.',
        ]);

        return redirect()->route('admin.patients.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        try {
            $patient->delete();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Paciente eliminado',
                'text' => 'El paciente ha sido eliminado exitosamente.',
            ]);

            return redirect()->route('admin.patients.index');
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el paciente. Por favor, intenta nuevamente.',
            ]);

            return redirect()->route('admin.patients.index');
        }
    }
}
