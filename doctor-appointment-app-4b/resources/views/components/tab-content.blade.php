@props(['tab', 'title' => null])

{{-- Panel de contenido que se muestra solo cuando coincide con la pestaña activa. (contenido mostrado/oculto según la pestaña activa)--}}
<div
    x-show="activeTab === '{{ $tab }}'"
    class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm"
    role="tabpanel"
>
    @if ($title)
        {{-- Titulo opcional de la sección dentro de la pestaña. --}}
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    @endif

    {{-- Contenido libre del panel. --}}
    {{ $slot }}
</div>
