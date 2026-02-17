{{-- Tab 3: Información General --}}
<x-wire-native-select label="Tipo de sangre" class="mb-4" name="blood_type_id">
    <option value="">Seleccione un tipo de sangre</option>
    @foreach ($bloodTypes as $bloodType)
        <option value="{{ $bloodType->id }}"  
            @selected(old('blood_type_id', $patient->blood_type_id) == $bloodType->id)>
            {{ $bloodType->name }}
        </option>
    @endforeach
</x-wire-native-select>

<x-wire-textarea label="Observaciones" name="observations" placeholder="Notas adicionales del paciente">
    {{ old('observations', $patient->observations) }}
</x-wire-textarea>

