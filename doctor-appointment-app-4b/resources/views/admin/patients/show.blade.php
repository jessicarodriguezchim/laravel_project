<x-admin-layout title="Pacientes | Detalle"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Pacientes', 'href' => route('admin.patients.index')],
    ['name' => 'Detalle']
]">

<x-slot name="actions">
    <x-wire-button href="{{ route('admin.patients.edit', $patient) }}" blue>
        <i class="fa-solid fa-pen-to-square"></i>
        <span class="ml-1">Editar</span>
    </x-wire-button>
</x-slot>

<div class="space-y-6">
    {{-- Información del usuario --}}
    <x-wire-card>
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fa-solid fa-user mr-2 text-blue-600"></i>
            Información personal
        </h2>
        
        <div class="grid lg:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Nombre completo</span>
                    <p class="text-gray-900">{{ $patient->user->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Correo electrónico</span>
                    <p class="text-gray-900">{{ $patient->user->email }}</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Teléfono</span>
                    <p class="text-gray-900">{{ $patient->user->phone ?? 'No registrado' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Dirección</span>
                    <p class="text-gray-900">{{ $patient->user->address ?? 'No registrada' }}</p>
                </div>
            </div>
        </div>
    </x-wire-card>

    {{-- Información médica --}}
    <x-wire-card>
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fa-solid fa-notes-medical mr-2 text-red-600"></i>
            Información médica
        </h2>
        
        <div class="grid lg:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Tipo de sangre</span>
                    <p class="text-gray-900">
                        @if($patient->bloodType)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $patient->bloodType->name }}
                            </span>
                        @else
                            <span class="text-gray-400">No registrado</span>
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Alergias</span>
                    <p class="text-gray-900">{{ $patient->allergies ?? 'Ninguna registrada' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Condiciones crónicas</span>
                    <p class="text-gray-900">{{ $patient->chronic_conditions ?? 'Ninguna registrada' }}</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Historial quirúrgico</span>
                    <p class="text-gray-900">{{ $patient->surgical_history ?? 'Sin historial' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Antecedentes familiares</span>
                    <p class="text-gray-900">{{ $patient->family_history ?? 'Sin antecedentes registrados' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Observaciones</span>
                    <p class="text-gray-900">{{ $patient->observations ?? 'Sin observaciones' }}</p>
                </div>
            </div>
        </div>
    </x-wire-card>

    {{-- Contacto de emergencia --}}
    <x-wire-card>
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fa-solid fa-phone-volume mr-2 text-green-600"></i>
            Contacto de emergencia
        </h2>
        
        @if($patient->emergency_contact_name || $patient->emergency_contact_phone)
            <div class="grid lg:grid-cols-3 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Nombre</span>
                    <p class="text-gray-900">{{ $patient->emergency_contact_name ?? 'No registrado' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Teléfono</span>
                    <p class="text-gray-900">{{ $patient->emergency_contact_phone ?? 'No registrado' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Parentesco</span>
                    <p class="text-gray-900">{{ $patient->emergency_contact_relationship ?? 'No registrado' }}</p>
                </div>
            </div>
        @else
            <p class="text-gray-400 italic">No se ha registrado contacto de emergencia</p>
        @endif
    </x-wire-card>

    {{-- Metadatos --}}
    <x-wire-card>
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fa-solid fa-clock-rotate-left mr-2 text-gray-600"></i>
            Registro
        </h2>
        
        <div class="grid lg:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Fecha de registro:</span>
                <span class="ml-2 text-gray-900">{{ $patient->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Última actualización:</span>
                <span class="ml-2 text-gray-900">{{ $patient->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </x-wire-card>
</div>

</x-admin-layout>
