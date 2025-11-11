@props([
    'href' => null,
    'type' => 'button',
    'blue' => false,
    'red' => false,
    'green' => false,
    'yellow' => false,
    'purple' => false,
    'xs' => false,
    'sm' => false,
    'lg' => false,
])

@php
    // Determine color classes
    $colorClasses = '';
    if ($blue) {
        $colorClasses = 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
    } elseif ($red) {
        $colorClasses = 'bg-red-600 hover:bg-red-700 focus:ring-red-500';
    } elseif ($green) {
        $colorClasses = 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
    } elseif ($yellow) {
        $colorClasses = 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500';
    } elseif ($purple) {
        $colorClasses = 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500';
    } else {
        $colorClasses = 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500';
    }
    
    // Determine size classes
    $sizeClasses = '';
    if ($xs) {
        $sizeClasses = 'px-2 py-1 text-xs';
    } elseif ($sm) {
        $sizeClasses = 'px-3 py-1.5 text-sm';
    } elseif ($lg) {
        $sizeClasses = 'px-6 py-3 text-base';
    } else {
        $sizeClasses = 'px-4 py-2 text-sm';
    }
    
    $baseClasses = 'inline-flex items-center justify-center border border-transparent rounded-md font-semibold text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150';
    $classes = "$baseClasses $colorClasses $sizeClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif






