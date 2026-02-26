@props(['id' => 'tabs', 'initialTab' => 'personal'])

{{-- Contenedor de tabs: inicializa Alpine con la pestaña activa por defecto. --}}
<div x-data="{ activeTab: '{{ $initialTab }}' }">

    {{-- Barra de navegación de pestañas --}}
    <div class="mb-6 border-b border-gray-200">
        <ul
            class="flex flex-wrap -mb-px text-sm font-medium text-center"
            id="{{ $id }}"
            role="tablist"
        >
            {{-- Slot dedicado a los botones/enlaces de navegación entre pestañas. --}}
            {{ $links }}
        </ul>
    </div>

    {{-- Paneles de contenido --}}
    <div>
        {{-- Slot principal con los paneles/tab-content. --}}
        {{ $slot }}
    </div>

</div>
