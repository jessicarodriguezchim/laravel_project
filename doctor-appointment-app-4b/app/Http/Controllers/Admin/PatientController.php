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
        return view('admin.patients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bloodTypes = BloodType::all();
        // Usuarios que no tienen un registro de paciente aún
        $users = User::query()
            ->whereDoesntHave('patient')
            ->role('Paciente')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();
        return view('admin.patients.create', compact('bloodTypes', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Sanitizar el teléfono: eliminar paréntesis, guiones y espacios
        if ($request->emergency_contact_phone) {
            $request->merge([
                'emergency_contact_phone' => preg_replace('/\D/', '', $request->emergency_contact_phone)
            ]);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:patients,user_id',
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|max:255',
            'chronic_conditions' => 'nullable|string|max:255',
            'surgical_history' => 'nullable|string|max:255',
            'family_history' => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|digits:10',
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
        try {
            // Sanitizar el teléfono: eliminar paréntesis, guiones y espacios
            if ($request->emergency_contact_phone) {
                $request->merge([
                    'emergency_contact_phone' => preg_replace('/\D/', '', $request->emergency_contact_phone)
                ]);
            }

            // 1. Validación estricta
            $data = $request->validate([
                'blood_type_id' => 'nullable|exists:blood_types,id',
                'allergies' => 'nullable|string|max:255',
                'chronic_conditions' => 'nullable|string|max:255',
                'surgical_history' => 'nullable|string|max:255',
                'family_history' => 'nullable|string|max:255',
                'observations' => 'nullable|string|max:255',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|digits:10',
                'emergency_contact_relationship' => 'nullable|string|max:255',
            ]);

            // 2. Intento de actualización
            $patient->update($data);

            // 3. Respuesta de éxito
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Paciente actualizado',
                'text' => 'El paciente ha sido actualizado exitosamente.',
            ]);

            return redirect()->route('admin.patients.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si falla la validación, Laravel vuelve atrás automáticamente
            throw $e;

        } catch (\Exception $e) {
            // Si hay un error de base de datos o de sistema
            return back()
                ->withInput()
                ->with('flash.banner', 'Error inesperado: No se pudo guardar la información.')
                ->with('flash.bannerStyle', 'danger');
        }
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
