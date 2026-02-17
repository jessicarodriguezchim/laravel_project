{{-- Tab 4: Contacto de Emergencia --}}
<div class="space-y-4">
    <x-wire-input 
        label="Nombre del contacto" 
        name="emergency_contact_name" 
        placeholder="Nombre completo del contacto"
        value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" 
    />
    
    <x-wire-phone 
        label="Teléfono de contacto" 
        name="emergency_contact_phone" 
        mask="(###) ###-####" 
        placeholder="(999) 999-9999"
        value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" 
    />
    
    <x-wire-input 
        label="Relación / Parentesco" 
        name="emergency_contact_relationship"
        placeholder="Ej. Esposa, Hermano, Padre, Amigo"
        value="{{ old('emergency_contact_relationship', $patient->emergency_contact_relationship) }}" 
    />
</div>

