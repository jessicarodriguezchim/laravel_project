@props([
    'title' => config('app.name', 'Laravel'),
    'breadcrumbs' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts -->  
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @livewireStyles

        <!-- Scripts principales -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/a7de8752fc.js" crossorigin="anonymous"></script>
        {{--sweetalert2 --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- ✅ WireUI Styles --}}

    </head>

    <body class="font-sans antialiased bg-gray-50">
        {{-- 🔹 Navbar superior --}}
        @include('layouts.includes.admin.navigation')

        {{-- 🔹 Sidebar lateral --}}
        @include('layouts.includes.admin.sidebar')

        <div class="p-4 sm:ml-64">
            <!-- Margin top 14px -->
            <div class="mt-14 flex items-center justify-between w-full">
                @include('layouts.includes.admin.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])
                @isset($actions)
                {{ $actions }}
                @endisset

            </div>

            {{-- Contenido dinámico de cada vista --}}
            {{ $slot }}
        </div>

        @stack('modals')

        {{-- 🔹 Scripts adicionales --}}
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

        {{-- Orden correcto: primero WireUI, luego Livewire --}}
        @wireUiScripts
        @livewireScripts

        {{--Mostrar Sweet Alert--}}
        @if (session('swal'))
        <script>
        Swal.fire(@json(session('swal')));
    </script>
    @endif
    <script>
        forms = document.querySelectorAll('.delete-form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    </body>
</html>