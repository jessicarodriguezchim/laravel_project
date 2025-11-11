@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
])

<div class="mb-4">
    @if($label)
        <x-label for="{{ $name }}" value="{{ $label }}" />
    @endif
    
    <x-input 
        id="{{ $name }}" 
        name="{{ $name }}" 
        type="{{ $type }}" 
        class="mt-1 block w-full" 
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        :disabled="$disabled"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except(['label', 'name', 'type', 'value', 'placeholder', 'required', 'disabled']) }}
    />
    
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

