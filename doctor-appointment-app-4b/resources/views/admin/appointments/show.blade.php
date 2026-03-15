<x-admin-layout title="Cita #{{ $appointment->id }}"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Detalle']
]">

<x-wire-card>
    {{-- Cabecera con información de la cita --}}
    <div class="lg:flex lg:justify-between lg:items-center mb-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fa-solid fa-calendar-check text-blue-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-2xl font-semibold text-gray-900">Cita #{{ $appointment->id }}</p>
                <p class="text-sm text-gray-500">
                    {{ $appointment->date->format('d/m/Y') }} | {{ date('H:i', strtotime($appointment->start_time)) }} - {{ date('H:i', strtotime($appointment->end_time)) }}
                </p>
            </div>
        </div>
        <div class="flex space-x-3 mt-6 lg:mt-0">
            <x-wire-button href="{{ route('admin.appointments.index') }}" white>
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Volver
            </x-wire-button>
            <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue>
                <i class="fa-solid fa-pen-to-square mr-2"></i>
                Editar
            </x-wire-button>
            <x-wire-button href="{{ route('admin.appointments.consultation', $appointment) }}" green>
                <i class="fa-solid fa-stethoscope mr-2"></i>
                Atender
            </x-wire-button>
        </div>
    </div>

    <hr class="my-6">

    {{-- Estado de la cita --}}
    <div class="mb-6">
        @php
            $statusColors = [
                1 => 'yellow',
                2 => 'blue',
                3 => 'green',
                4 => 'red',
            ];
            $statusNames = [
                1 => 'Pendiente',
                2 => 'Confirmada',
                3 => 'Completada',
                4 => 'Cancelada',
            ];
            $color = $statusColors[$appointment->status] ?? 'gray';
            $statusName = $statusNames[$appointment->status] ?? 'Desconocido';
        @endphp
        <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
            {{ $statusName }}
        </span>
    </div>

    {{-- Información del Paciente --}}
    <div class="space-y-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fa-solid fa-user mr-2"></i>
            Información del Paciente
        </h3>

        <div class="grid lg:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Nombre</p>
                <p class="text-base text-gray-900">{{ $appointment->patient->user->name }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Email</p>
                <p class="text-base text-gray-900">{{ $appointment->patient->user->email }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Teléfono</p>
                <p class="text-base text-gray-900">{{ $appointment->patient->user->phone ?? 'No registrado' }}</p>
            </div>
        </div>
    </div>

    <hr class="my-6">

    {{-- Información del Doctor --}}
    <div class="space-y-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fa-solid fa-user-doctor mr-2"></i>
            Información del Doctor
        </h3>

        <div class="grid lg:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Nombre</p>
                <p class="text-base text-gray-900">{{ $appointment->doctor->user->name }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Especialidad</p>
                <p class="text-base text-gray-900">{{ $appointment->doctor->speciality?->name ?? 'No asignada' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Email</p>
                <p class="text-base text-gray-900">{{ $appointment->doctor->user->email }}</p>
            </div>
        </div>
    </div>

    <hr class="my-6">

    {{-- Detalles de la Cita --}}
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fa-solid fa-clipboard mr-2"></i>
            Detalles de la Cita
        </h3>

        <div class="grid lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Fecha</p>
                <p class="text-base text-gray-900">{{ $appointment->date->format('d/m/Y') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Hora de inicio</p>
                <p class="text-base text-gray-900">{{ date('H:i', strtotime($appointment->start_time)) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Hora de fin</p>
                <p class="text-base text-gray-900">{{ date('H:i', strtotime($appointment->end_time)) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Duración</p>
                <p class="text-base text-gray-900">{{ $appointment->duration }} minutos</p>
            </div>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-500">Motivo de la consulta</p>
            <p class="text-base text-gray-900">{{ $appointment->reason ?? 'No especificado' }}</p>
        </div>
    </div>
</x-wire-card>

</x-admin-layout>
