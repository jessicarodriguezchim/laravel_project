{{-- 1. Lógica de PHP: Mantenemos la de tu compañero pero aseguramos los campos de la Parte 10 --}}
@php
$errorGrupos = [
    'datos-personales' => ['document_number', 'birth_date', 'gender', 'city', 'state'],
    'antecedentes' => ['allergies', 'chronic_conditions', 'surgical_history', 'family_history'],
    'informacion-general' => ['blood_type_id', 'observations'],
    'contacto-emergencia' => [
        'emergency_contact_name', 
        'emergency_contact_phone', 
        'emergency_contact_relationship'
    ],
];

$initialTab = 'datos-personales';
foreach ($errorGrupos as $tabName => $fields) {
    if($errors->hasAny($fields)) {
        $initialTab = $tabName;
        break;
    }
}
@endphp

<x-admin-layout title="Editar Paciente | {{ $patient->user->name }}">
    
    {{-- Formulario con Método PUT (Parte 11) --}}
    <form action="{{ route('admin.patients.update', $patient) }}" method="POST" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf
        @method('PUT')

        {{-- Cabecera de Acciones --}}
        <x-wire-card class="mt-10">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center gap-4">
                    <img src="{{ $patient->user->profile_photo_url }}" class="h-20 w-20 rounded-full object-cover">
                    <div>
                        <p class="text-2xl font-semibold text-gray-900">{{ $patient->user->name }}</p>
                        <!-- <p class="text-sm text-gray-500 italic">Paciente ID: {{ $patient->id }}</p> -->
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button href="{{ route('admin.patients.index') }}" white>Volver</x-wire-button>
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

        {{-- Resumen Global de Errores - Parte 12 --}}
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

        {{-- Pestañas y Contenido --}}
        <x-wire-card class="mt-6">
            <div x-data="{ tab: '{{ $initialTab }}' }">
                
                {{-- Navegación de Tabs (Lógica de colores y alertas) --}}
                <div class="border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium">
                        @foreach(['datos-personales' => 'User', 'antecedentes' => 'File-Lines', 'informacion-general' => 'Info', 'contacto-emergencia' => 'Heart'] as $id => $icon)
                            @php $hasError = $errors->hasAny($errorGrupos[$id] ?? []); @endphp
                            <li class="me-2">
                                <a href="#" x-on:click.prevent="tab = '{{ $id }}'"
                                   :class="{
                                       'text-blue-600 border-blue-600': tab === '{{ $id }}' && !{{ $hasError ? 'true' : 'false' }},
                                       'text-red-600 border-red-600 font-bold': {{ $hasError ? 'true' : 'false' }},
                                       'border-transparent text-gray-500 hover:text-blue-400': tab !== '{{ $id }}' && !{{ $hasError ? 'true' : 'false' }}
                                   }"
                                   class="inline-flex items-center p-4 border-b-2 rounded-t-lg transition-all duration-200">
                                    <i class="fa-solid fa-{{ strtolower($icon) }} me-2"></i>
                                    {{ ucwords(str_replace('-', ' ', $id)) }}
                                    @if($hasError)
                                        <i class="fa-solid fa-circle-exclamation ms-2 animate-pulse"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contenido de las Pestañas --}}
                <div class="p-4 mt-4">
                    {{-- Aquí incluyes los bloques de la Parte 8, 9 y 10 que vimos antes --}}
                    
                    <div x-show="tab === 'datos-personales'" x-transition>
                        @include('admin.patients.partials.edit-tab-personales')
                    </div>

                    <div x-show="tab === 'antecedentes'" style="display:none;" x-transition>
                        @include('admin.patients.partials.edit-tab-antecedentes')
                    </div>

                    <div x-show="tab === 'informacion-general'" style="display:none;" x-transition>
                        @include('admin.patients.partials.edit-tab-general')
                    </div>

                    <div x-show="tab === 'contacto-emergencia'" style="display:none;" x-transition>
                        @include('admin.patients.partials.edit-tab-emergencia')
                    </div>
                </div>
            </div>
        </x-wire-card>
    </form>
</x-admin-layout>