<x-admin-layout title="Editar Cita"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Editar']
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

{{-- Formulario con Método PUT --}}
<form action="{{ route('admin.appointments.update', $appointment) }}" method="POST" x-data="{ submitting: false }" x-on:submit="submitting = true">
    @csrf
    @method('PUT')

    {{-- Cabecera de Acciones --}}
    <x-wire-card class="mt-10">
        <div class="lg:flex lg:justify-between lg:items-center">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-gray-900">Cita #{{ $appointment->id }}</p>
                    <p class="text-sm text-gray-500">{{ $appointment->date->format('d/m/Y') }} - {{ date('H:i', strtotime($appointment->start_time)) }}</p>
                </div>
            </div>
            <div class="flex space-x-3 mt-6 lg:mt-0">
                <x-wire-button href="{{ route('admin.appointments.index') }}" white>Volver</x-wire-button>
                <x-wire-button type="submit" blue x-bind:disabled="submitting">
                    <span x-show="!submitting">
                        <i class="fa-solid fa-floppy-disk mr-2"></i>
                        Guardar cambios
                    </span>
                    <span x-show="submitting" style="display: none;">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                        Guardando...
                    </span>
                </x-wire-button>
            </div>
        </div>
    </x-wire-card>

    {{-- Resumen Global de Errores --}}
    @if ($errors->any())
        <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-md">
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

    {{-- Formulario de Edición --}}
    <x-wire-card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">
            <i class="fa-solid fa-calendar-days mr-2"></i>
            Información de la Cita
        </h3>

        <div class="space-y-6">
            {{-- Selección de Paciente y Doctor --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="patient_id" label="Paciente" required>
                        <option value="">Seleccione un paciente</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                {{ $patient->user->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <div>
                    <x-wire-native-select name="doctor_id" label="Doctor" required>
                        <option value="">Seleccione un doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
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
                        :value="old('date', $appointment->date->format('Y-m-d'))"
                        min="{{ date('Y-m-d') }}"
                        required
                    />
                </div>

                <div>
                    <x-wire-input
                        type="time"
                        name="start_time"
                        label="Hora de inicio"
                        :value="old('start_time', date('H:i', strtotime($appointment->start_time)))"
                        required
                    />
                </div>

                <div>
                    <x-wire-input
                        type="time"
                        name="end_time"
                        label="Hora de fin"
                        :value="old('end_time', date('H:i', strtotime($appointment->end_time)))"
                        required
                    />
                </div>
            </div>

            {{-- Estado --}}
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="status" label="Estado">
                        <option value="1" @selected(old('status', $appointment->status) == 1)>Pendiente</option>
                        <option value="2" @selected(old('status', $appointment->status) == 2)>Confirmada</option>
                        <option value="3" @selected(old('status', $appointment->status) == 3)>Completada</option>
                        <option value="4" @selected(old('status', $appointment->status) == 4)>Cancelada</option>
                    </x-wire-native-select>
                </div>
            </div>

            {{-- Motivo --}}
            <div>
                <x-wire-textarea
                    name="reason"
                    label="Motivo de la cita"
                    placeholder="Describa el motivo de la consulta..."
                    rows="4"
                    required
                >{{ old('reason', $appointment->reason) }}</x-wire-textarea>
            </div>
        </div>
    </x-wire-card>
</form>
</x-admin-layout>
