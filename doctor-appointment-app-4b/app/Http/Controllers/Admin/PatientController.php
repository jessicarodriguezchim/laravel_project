<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\PatientsImportDuplicateScanner;
use App\Jobs\ProcessPatientsImport;
use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.patients.index', [
            'trackImportProgress' => session()->has('current_import_id'),
        ]);
    }

    public function importForm()
    {
        if (function_exists('set_time_limit')) {
            set_time_limit(120);
        }

        return view('admin.patients.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:102400',
        ]);

        $path = $request->file('file')->store('imports', 'local');

        try {
            $sheets = Excel::toArray(new HeadingRowImport, $path, 'local');
            $headings = array_values((array) ($sheets[0][0] ?? []));
            $headings = array_map(fn ($h) => strtolower((string) $h), $headings);

            if ($headings === []) {
                Storage::disk('local')->delete($path);

                return redirect()
                    ->route('admin.patients.index')
                    ->withErrors(['file' => __('The uploaded file is empty or invalid.')]);
            }

            $emailKeys = ['email', 'correo', 'correo_electronico'];
            $nameKeys = ['name', 'nombre', 'nombre_completo', 'full_name'];
            $hasEmail = count(array_intersect($headings, $emailKeys)) > 0
                || collect($headings)->contains(fn ($h) => str_starts_with($h, 'correo'));
            $hasName = count(array_intersect($headings, $nameKeys)) > 0;

            if (! $hasEmail || ! $hasName) {
                Storage::disk('local')->delete($path);

                return redirect()
                    ->route('admin.patients.index')
                    ->withErrors([
                        'file' => __('The file must include name (or nombre / nombre_completo) and email (or correo) columns.'),
                    ]);
            }

            $duplicateScanner = new PatientsImportDuplicateScanner;
            Excel::import($duplicateScanner, $path, 'local');
            if ($dupMessage = $duplicateScanner->conflictMessage()) {
                Storage::disk('local')->delete($path);

                return redirect()
                    ->route('admin.patients.index')
                    ->withErrors(['file' => $dupMessage]);
            }
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);

            return redirect()
                ->route('admin.patients.index')
                ->withErrors(['file' => __('Could not read the spreadsheet. Check that the file is not corrupted.')]);
        }

        $importId = uniqid('import_', true);
        session()->put('current_import_id', $importId);
        Cache::put($importId, [
            'current' => 0,
            'total' => 1,
            'status' => 'processing',
            'imported' => 0,
            'skipped' => 0,
        ], 3600);

        ProcessPatientsImport::dispatch($path, $importId);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => __('Import queued'),
            'text' => __('Processing in the background. Watch the progress bar below.'),
        ]);

        return redirect()->route('admin.patients.index');
    }

    public function importProgress()
    {
        $importId = session('current_import_id');
        if (! $importId) {
            return response()->json(['status' => 'none']);
        }

        $data = Cache::get($importId);
        if (! $data) {
            return response()->json(['status' => 'none']);
        }

        if (in_array($data['status'] ?? '', ['finished', 'error'], true)) {
            session()->forget('current_import_id');
        }

        return response()->json($data);
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
                'emergency_contact_phone' => preg_replace('/\D/', '', $request->emergency_contact_phone),
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
                    'emergency_contact_phone' => preg_replace('/\D/', '', $request->emergency_contact_phone),
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
     * Delete every patient record (citas asociadas se eliminan en cascada).
     * Los usuarios vinculados no se borran (igual que al eliminar un paciente individual).
     */
    public function destroyAll()
    {
        try {
            $deleted = 0;
            DB::transaction(function () use (&$deleted) {
                $deleted = Patient::query()->delete();
            });

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Pacientes eliminados',
                'text' => $deleted === 0
                    ? 'No había pacientes registrados.'
                    : "Se eliminaron {$deleted} paciente(s). Las citas vinculadas también se eliminaron.",
            ]);

            return redirect()->route('admin.patients.index');
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudieron eliminar los pacientes. Intenta de nuevo.',
            ]);

            return redirect()->route('admin.patients.index');
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
