<x-admin-layout title="Citas | Nueva"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Nueva']
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
    <h2 class="text-lg font-semibold mb-4">
        <i class="fa-solid fa-calendar-plus mr-2"></i>
        Registrar nueva cita
    </h2>

    {{-- Resumen Global de Errores --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">
                        Se encontraron {{ $errors->count() }} errores de validación
                    </h3>
                    <div class="mt-1 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.appointments.store') }}" method="POST">
        @csrf

        <div class="space-y-4">
            {{-- Selección de Paciente y Doctor --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="patient_id" label="Paciente" required>
                        <option value="">Seleccione un paciente</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                                {{ $patient->user->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <div>
                    <x-wire-native-select name="doctor_id" label="Doctor" required>
                        <option value="">Seleccione un doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>
                                {{ $doctor->user->name }} - {{ $doctor->speciality?->name ?? 'Sin especialidad' }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>
            </div>

            {{-- Fecha y Horas --}}
            <div class="grid lg:grid-cols-3 gap-4">
                <div>
                    <x-wire-input
                        type="date"
                        name="date"
                        label="Fecha de la cita"
                        :value="old('date')"
                        min="{{ date('Y-m-d') }}"
                        required
                    />
                </div>

                <div>
                    <x-wire-input
                        type="time"
                        name="start_time"
                        label="Hora de inicio"
                        :value="old('start_time')"
                        required
                    />
                </div>

                <div>
                    <x-wire-input
                        type="time"
                        name="end_time"
                        label="Hora de fin"
                        :value="old('end_time')"
                        required
                    />
                </div>
            </div>

            {{-- Estado --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="status" label="Estado">
                        <option value="1" @selected(old('status', 1) == 1)>Pendiente</option>
                        <option value="2" @selected(old('status') == 2)>Confirmada</option>
                        <option value="3" @selected(old('status') == 3)>Completada</option>
                        <option value="4" @selected(old('status') == 4)>Cancelada</option>
                    </x-wire-native-select>
                </div>
            </div>

            {{-- Motivo de la cita --}}
            <div>
                <x-wire-textarea
                    name="reason"
                    label="Motivo de la cita"
                    :value="old('reason')"
                    placeholder="Describa el motivo de la consulta..."
                    rows="4"
                    required
                />
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancelar
                </a>
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cita
                </x-wire-button>
            </div>
        </div>
    </form>
</x-wire-card>

</x-admin-layout>
