<x-admin-layout title="Pacientes | Crear"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Pacientes', 'href' => route('admin.patients.index')],
    ['name' => 'Nuevo']
]">

<x-wire-card>
    <h2 class="text-lg font-semibold mb-4">Registrar nuevo paciente</h2>

    <form action="{{ route('admin.patients.store') }}" method="POST">
        @csrf

        <div class="space-y-4">
            {{-- Selección de usuario --}}
            <div>
                <x-wire-native-select name="user_id" label="Usuario" required>
                    <option value="">Seleccione un usuario</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                            {{ $user->name }} - {{ $user->email }}
                        </option>
                    @endforeach
                </x-wire-native-select>
                <p class="text-sm text-gray-500">Solo se muestran usuarios con rol "Paciente" sin registro de paciente</p>
            </div>

            {{-- Información médica básica --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="blood_type_id" label="Tipo de sangre">
                        <option value="">Seleccione un tipo de sangre</option>
                        @foreach($bloodTypes as $bloodType)
                            <option value="{{ $bloodType->id }}" @selected(old('blood_type_id') == $bloodType->id)>
                                {{ $bloodType->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <x-wire-input
                    name="allergies"
                    label="Alergias"
                    :value="old('allergies')"
                    placeholder="Ej. Penicilina, Polen, Mariscos"
                />
            </div>

            {{-- Historial médico --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <x-wire-input
                    name="chronic_conditions"
                    label="Condiciones crónicas"
                    :value="old('chronic_conditions')"
                    placeholder="Ej. Diabetes, Hipertensión"
                />

                <x-wire-input
                    name="surgical_history"
                    label="Historial quirúrgico"
                    :value="old('surgical_history')"
                    placeholder="Ej. Apendicectomía 2020"
                />
            </div>

            <div class="grid lg:grid-cols-2 gap-4">
                <x-wire-input
                    name="family_history"
                    label="Antecedentes familiares"
                    :value="old('family_history')"
                    placeholder="Ej. Diabetes (padre), Cáncer (abuelo)"
                />

                <x-wire-input
                    name="observations"
                    label="Observaciones"
                    :value="old('observations')"
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
                        :value="old('emergency_contact_name')"
                        placeholder="Nombre del contacto"
                    />

                    <x-wire-input
                        name="emergency_contact_phone"
                        label="Teléfono"
                        :value="old('emergency_contact_phone')"
                        placeholder="Ej. 1234567890"
                        inputmode="tel"
                    />

                    <x-wire-input
                        name="emergency_contact_relationship"
                        label="Parentesco"
                        :value="old('emergency_contact_relationship')"
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
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Paciente
                </x-wire-button>
            </div>
        </div>
    </form>
</x-wire-card>

</x-admin-layout>
