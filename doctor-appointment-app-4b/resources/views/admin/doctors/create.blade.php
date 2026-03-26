<x-admin-layout title="Doctores | Crear"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
    ['name' => 'Nuevo']
]">

{{-- Estilo para placeholder gris --}}
<style>
    textarea::placeholder {
        color: #9ca3af !important;
        opacity: 1 !important;
    }
    input::placeholder {
        color: #9ca3af !important;
        opacity: 1 !important;
    }
</style>

<x-wire-card>
    <h2 class="text-lg font-semibold mb-4">Registrar nuevo doctor</h2>

    <form action="{{ route('admin.doctors.store') }}" method="POST">
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
                <p class="text-sm text-gray-500">Solo se muestran usuarios con rol "Doctor" sin registro de doctor</p>
            </div>

            {{-- Información profesional --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="speciality_id" label="Especialidad">
                        <option value="">Seleccione una especialidad</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}" @selected(old('speciality_id') == $speciality->id)>
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <x-wire-input
                    name="license_number"
                    label="Número de licencia"
                    :value="old('license_number')"
                    placeholder="Ej. MED-12345"
                />
            </div>

            {{-- Biografía --}}
            <div>
                <x-wire-textarea
                    name="biography"
                    label="Biografía"
                    :value="old('biography')"
                    placeholder="Escriba una breve biografía del doctor..."
                    rows="4"
                />
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancelar
                </a>
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Doctor
                </x-wire-button>
            </div>
        </div>
    </form>
</x-wire-card>

</x-admin-layout>

