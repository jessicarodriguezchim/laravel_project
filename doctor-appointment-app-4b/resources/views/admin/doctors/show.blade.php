<x-admin-layout title="Doctor | {{ $doctor->user->name }}"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
    ['name' => $doctor->user->name]
]">

<x-wire-card>
    {{-- Cabecera con información del doctor --}}
    <div class="lg:flex lg:justify-between lg:items-center mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ $doctor->user->profile_photo_url }}" class="h-20 w-20 rounded-full object-cover">
            <div>
                <p class="text-2xl font-semibold text-gray-900">{{ $doctor->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $doctor->user->email }}</p>
            </div>
        </div>
        <div class="flex space-x-3 mt-6 lg:mt-0">
            <x-wire-button href="{{ route('admin.doctors.index') }}" white>
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Volver
            </x-wire-button>
            <x-wire-button href="{{ route('admin.doctors.edit', $doctor) }}" blue>
                <i class="fa-solid fa-pen-to-square mr-2"></i>
                Editar
            </x-wire-button>
        </div>
    </div>

    <hr class="my-6">

    {{-- Información profesional --}}
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fa-solid fa-user-doctor mr-2"></i>
            Información Profesional
        </h3>

        <div class="grid lg:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Especialidad</p>
                <p class="text-base text-gray-900">{{ $doctor->speciality?->name ?? 'No asignada' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Número de Licencia</p>
                <p class="text-base text-gray-900">{{ $doctor->license_number ?? 'No registrado' }}</p>
            </div>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-500">Biografía</p>
            <p class="text-base text-gray-900">{{ $doctor->biography ?? 'Sin biografía' }}</p>
        </div>
    </div>

    <hr class="my-6">

    {{-- Información de contacto del usuario --}}
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fa-solid fa-address-card mr-2"></i>
            Información de Contacto
        </h3>

        <div class="grid lg:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Teléfono</p>
                <p class="text-base text-gray-900">{{ $doctor->user->phone ?? 'No registrado' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Número de identificación</p>
                <p class="text-base text-gray-900">{{ $doctor->user->id_number ?? 'No registrado' }}</p>
            </div>

            <div class="lg:col-span-2">
                <p class="text-sm font-medium text-gray-500">Dirección</p>
                <p class="text-base text-gray-900">{{ $doctor->user->address ?? 'No registrada' }}</p>
            </div>
        </div>
    </div>
</x-wire-card>

</x-admin-layout>

