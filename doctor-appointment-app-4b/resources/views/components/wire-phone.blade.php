@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => '(999) 999-9999',
    'mask' => '(###) ###-####',
    'required' => false,
    'disabled' => false,
])

<div class="mb-4">
    @if($label)
        <x-label for="{{ $name }}" value="{{ $label }}" />
    @endif
    
    <input 
        id="{{ $name }}" 
        name="{{ $name }}" 
        type="tel"
        x-data="{
            value: '{{ $value ?? '' }}',
            mask: '{{ $mask }}',
            format(e) {
                let input = e.target.value.replace(/\D/g, '');
                let formatted = '';
                let maskIndex = 0;
                
                for (let i = 0; i < input.length && maskIndex < this.mask.length; i++) {
                    while (maskIndex < this.mask.length && this.mask[maskIndex] !== '#') {
                        formatted += this.mask[maskIndex];
                        maskIndex++;
                    }
                    if (maskIndex < this.mask.length) {
                        formatted += input[i];
                        maskIndex++;
                    }
                }
                
                this.value = formatted;
                e.target.value = formatted;
            }
        }"
        x-model="value"
        x-on:input="format($event)"
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        placeholder="{{ $placeholder }}"
        @if($disabled) disabled @endif
        @if($required) required @endif
        {{ $attributes->except(['label', 'name', 'value', 'placeholder', 'mask', 'required', 'disabled']) }}
    />
    
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

