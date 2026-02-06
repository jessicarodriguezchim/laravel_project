<x-admin-layout title="Pacientes | Editar"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Pacientes', 'href' => route('admin.patients.index')],
    ['name' => 'Editar']
]">

<x-wire-card>
    <h2 class="text-lg font-semibold mb-4">Editar paciente: {{ $patient->user->name }}</h2>

    <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            {{-- Información del usuario (solo lectura) --}}
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-md font-semibold mb-2 text-gray-700">Información del usuario</h3>
                <div class="grid lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600">Nombre:</span>
                        <span class="ml-2">{{ $patient->user->name }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Email:</span>
                        <span class="ml-2">{{ $patient->user->email }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Teléfono:</span>
                        <span class="ml-2">{{ $patient->user->phone ?? 'No registrado' }}</span>
                    </div>
                </div>
            </div>

            {{-- Información médica básica --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="blood_type_id" label="Tipo de sangre">
                        <option value="">Seleccione un tipo de sangre</option>
                        @foreach($bloodTypes as $bloodType)
                            <option value="{{ $bloodType->id }}" @selected(old('blood_type_id', $patient->blood_type_id) == $bloodType->id)>
                                {{ $bloodType->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <x-wire-input
                    name="allergies"
                    label="Alergias"
                    :value="old('allergies', $patient->allergies)"
                    placeholder="Ej. Penicilina, Polen, Mariscos"
                />
            </div>

            {{-- Historial médico --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <x-wire-input
                    name="chronic_conditions"
                    label="Condiciones crónicas"
                    :value="old('chronic_conditions', $patient->chronic_conditions)"
                    placeholder="Ej. Diabetes, Hipertensión"
                />

                <x-wire-input
                    name="surgical_history"
                    label="Historial quirúrgico"
                    :value="old('surgical_history', $patient->surgical_history)"
                    placeholder="Ej. Apendicectomía 2020"
                />
            </div>

            <div class="grid lg:grid-cols-2 gap-4">
                <x-wire-input
                    name="family_history"
                    label="Antecedentes familiares"
                    :value="old('family_history', $patient->family_history)"
                    placeholder="Ej. Diabetes (padre), Cáncer (abuelo)"
                />

                <x-wire-input
                    name="observations"
                    label="Observaciones"
                    :value="old('observations', $patient->observations)"
                    placeholder="Notas adicionales del paciente"
                />
            </div>

            {{-- Contacto de emergencia --}}
            <div class="border-t pt-4">
                <h3 class="text-md font-semibold mb-3 text-gray-700">Contacto de emergencia</h3>
                
                <div class="grid lg:grid-cols-3 gap-4">
                    <x-wire-input
                        name="emergency_contact_name"
                        label="Nombre"
                        :value="old('emergency_contact_name', $patient->emergency_contact_name)"
                        placeholder="Nombre del contacto"
                    />

                    <x-wire-input
                        name="emergency_contact_phone"
                        label="Teléfono"
                        :value="old('emergency_contact_phone', $patient->emergency_contact_phone)"
                        placeholder="Ej. 1234567890"
                        inputmode="tel"
                    />

                    <x-wire-input
                        name="emergency_contact_relationship"
                        label="Parentesco"
                        :value="old('emergency_contact_relationship', $patient->emergency_contact_relationship)"
                        placeholder="Ej. Esposa, Hermano, Padre"
                    />
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.patients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancelar
                </a>
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-floppy-disk"></i> Actualizar Paciente
                </x-wire-button>
            </div>
        </div>
    </form>
</x-wire-card>

</x-admin-layout>
