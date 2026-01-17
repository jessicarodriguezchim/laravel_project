@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'disabled' => false,
])

<div class="mb-4">
    @if($label)
        <x-label for="{{ $name }}" value="{{ $label }}" />
    @endif
    
    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['label', 'name', 'required', 'disabled']) }}
    >
        {{ $slot }}
    </select>
    
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
