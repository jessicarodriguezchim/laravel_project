<x-admin-layout
    light
    title="Pacientes | Importación masiva"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Pacientes', 'href' => route('admin.patients.index')],
        ['name' => 'Importar'],
    ]">

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-2 text-lg font-semibold">Bulk import (Excel / CSV)</h2>
        <p class="mb-4 text-sm text-gray-600">
            Upload a file with a header row. Required columns: <strong>name</strong> / nombre, <strong>email</strong> / correo,
            identification (<strong>id_number</strong>, cédula, or Spanish headers like <strong>Número de ID</strong> → <code class="text-xs">numero_de_id</code>),
            phone (<strong>phone</strong> / teléfono). Optional: <strong>address</strong>,
            <strong>blood_type</strong> (e.g. A+, O-), allergies, chronic_conditions, surgical_history,
            family_history, observations, emergency_contact_name, emergency_contact_phone, emergency_contact_relationship.
            Spanish column names (nombre, correo, …) are also accepted.
            Processing runs in the background — keep <code class="rounded bg-gray-100 px-1 text-xs">php artisan queue:work</code> running.
        </p>

        <form action="{{ route('admin.patients.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">File (.csv, .xlsx, .xls)</label>
                <input type="file" name="file" required
                    accept=".csv,.xlsx,.xls,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv"
                    class="block w-full cursor-pointer rounded-lg border border-gray-300 text-sm text-gray-900" />
            </div>
            @error('file')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-blue-700">
                    Queue import
                </button>
                <a href="{{ route('admin.patients.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
