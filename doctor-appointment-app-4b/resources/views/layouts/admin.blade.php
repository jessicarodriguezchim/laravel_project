@props([
    'title' => config('app.name', 'Laravel')
    'breadcrumbs' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config($title) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        //Inclusión de FontAwesome: permite usar íconos profesionales en toda la interfaz.
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/84edefe970.js" crossorigin="anonymous">
        </script>

        <wireui:scripts />
        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased pg-gray-50">

        @stack('modals')
 //rutas - Inclusión de la navegación superior y sidebar
        @include('layouts.includes.admin.navigation')
        @include('layouts.includes.admin.sidebar')

<div class="p-4 sm:ml-64">
    <!--Margin top 14px-->
    {{--Configuración del margen izquierdo responsive--}}
    <div class="mt-14 flex items-center justify-between w-full">
        @include('layouts.includes.admin.breadcrum')
        </div>
        {{ $slot }} <!--slot: espacio donde se pone algo; dentro de este slot va a ir el contenido de la pagina que use este layout-->
</div>

        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    </body>
</html>
