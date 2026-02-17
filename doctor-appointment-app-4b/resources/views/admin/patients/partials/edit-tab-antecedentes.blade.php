{{-- Tab 2: Antecedentes Médicos --}}
<div class="grid lg:grid-cols-2 gap-4">
    <div>
        <x-wire-textarea label="Alergias conocidas" name="allergies" placeholder="Ej. Penicilina, Polen, Mariscos">
            {{ old('allergies', $patient->allergies) }}
        </x-wire-textarea>
    </div>
    <div>
        <x-wire-textarea label="Enfermedades / Condiciones crónicas" name="chronic_conditions" placeholder="Ej. Diabetes, Hipertensión">
            {{ old('chronic_conditions', $patient->chronic_conditions) }}
        </x-wire-textarea>
    </div>
    <div>
        <x-wire-textarea label="Antecedentes quirúrgicos" name="surgical_history" placeholder="Ej. Apendicectomía 2020">
            {{ old('surgical_history', $patient->surgical_history) }}
        </x-wire-textarea>
    </div>
    <div>
        <x-wire-textarea label="Antecedentes familiares" name="family_history" placeholder="Ej. Diabetes (padre), Cáncer (abuelo)">
            {{ old('family_history', $patient->family_history) }}
        </x-wire-textarea>
    </div>                       
</div>

