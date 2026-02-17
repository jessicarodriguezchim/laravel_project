{{-- Tab 1: Datos Personales --}}
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        {{-- Lado izquierdo --}}
        <div class="flex items-start">
            <i class="fa-solid fa-user-gear text-blue-500 text-xl mt-1"></i>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-blue-800">
                    Edición de cuenta de usuario
                </h3>
                <div class="mt-1 text-sm text-blue-600">
                    Los datos de acceso (nombre, email y contraseña) deben gestionarse desde la cuenta de usuario asociada.
                </div>
            </div>
        </div>

        {{-- Lado derecho --}}
        <div class="flex-shrink-0 mt-4 sm:mt-0">
            <x-wire-button blue sm 
                href="{{ route('admin.users.edit', $patient->user) }}" 
                target="_blank">
                Editar usuario
                <i class="fa-solid fa-arrow-up-right-from-square ms-2"></i>
            </x-wire-button>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-4">
    <div>
        <span class="text-gray-500 font-semibold">Nombre: </span>
        <span class="text-gray-900 text-sm ml-1">{{ $patient->user->name }}</span>
    </div>
    <div>
        <span class="text-gray-500 font-semibold">Email: </span>
        <span class="text-gray-900 text-sm ml-1">{{ $patient->user->email }}</span>
    </div>
    <div>
        <span class="text-gray-500 font-semibold">Teléfono: </span>
        <span class="text-gray-900 text-sm ml-1">{{ $patient->user->phone ?? 'No registrado' }}</span>
    </div>
    <div>
        <span class="text-gray-500 font-semibold">Dirección: </span>
        <span class="text-gray-900 text-sm ml-1">{{ $patient->user->address ?? 'No registrada' }}</span>
    </div>
</div>

