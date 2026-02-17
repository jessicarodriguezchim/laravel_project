@props([
    'label' => null,
    'name' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'rows' => 3,
])

<div class="mb-4">
    @if($label)
        <x-label for="{{ $name }}" value="{{ $label }}" />
    @endif
    
    <textarea 
        id="{{ $name }}" 
        name="{{ $name }}" 
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($disabled) disabled @endif
        @if($required) required @endif
        {{ $attributes->except(['label', 'name', 'placeholder', 'required', 'disabled', 'rows']) }}
    >{{ $slot }}</textarea>
    
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

