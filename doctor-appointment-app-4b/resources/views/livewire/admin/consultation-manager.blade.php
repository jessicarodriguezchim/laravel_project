<div>
    {{-- Cabecera con información de la cita --}}
    <x-wire-card class="mb-6">
        <div class="lg:flex lg:justify-between lg:items-center">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-stethoscope text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-gray-900">Atención Médica</p>
                    <p class="text-sm text-gray-500">
                        Paciente: {{ $appointment->patient->user->name }} |
                        {{ $appointment->date->format('d/m/Y') }} - {{ date('H:i', strtotime($appointment->start_time)) }}
                    </p>
                </div>
            </div>
            <div class="flex space-x-3 mt-6 lg:mt-0">
                <x-wire-button href="{{ route('admin.patients.show', $appointment->patient) }}" blue>
                    <i class="fa-solid fa-folder-open mr-2"></i>
                    Ver Historia
                </x-wire-button>
                <x-wire-button wire:click="openHistoryModal" white>
                    <i class="fa-solid fa-clock-rotate-left mr-2"></i>
                    Consultas Anteriores
                </x-wire-button>
                <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Volver
                </a>
            </div>
        </div>
    </x-wire-card>

    {{-- Información del paciente y doctor --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <x-wire-card>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fa-solid fa-user mr-2"></i>
                Paciente
            </h3>
            <div class="space-y-2">
                <p><span class="font-medium text-gray-500">Nombre:</span> {{ $appointment->patient->user->name }}</p>
                <p><span class="font-medium text-gray-500">Email:</span> {{ $appointment->patient->user->email }}</p>
                <p><span class="font-medium text-gray-500">Teléfono:</span> {{ $appointment->patient->user->phone ?? 'No registrado' }}</p>
                @if($appointment->patient->allergies)
                    <p><span class="font-medium text-red-500">Alergias:</span> {{ $appointment->patient->allergies }}</p>
                @endif
            </div>
        </x-wire-card>

        <x-wire-card>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fa-solid fa-user-doctor mr-2"></i>
                Doctor
            </h3>
            <div class="space-y-2">
                <p><span class="font-medium text-gray-500">Nombre:</span> {{ $appointment->doctor->user->name }}</p>
                <p><span class="font-medium text-gray-500">Especialidad:</span> {{ $appointment->doctor->speciality?->name ?? 'No asignada' }}</p>
                <p><span class="font-medium text-gray-500">Motivo de consulta:</span> {{ $appointment->reason ?? 'No especificado' }}</p>
            </div>
        </x-wire-card>
    </div>

    {{-- Resumen Global de Errores --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">
                        Se encontraron {{ $errors->count() }} errores de validación
                    </h3>
                    <div class="mt-1 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Pestañas --}}
    <x-wire-card>
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button
                    wire:click="setTab('consultation')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'consultation' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <i class="fa-solid fa-notes-medical mr-2"></i>
                    Consulta
                </button>
                <button
                    wire:click="setTab('prescription')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'prescription' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <i class="fa-solid fa-prescription mr-2"></i>
                    Receta
                    @if(count($prescription) > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">{{ count($prescription) }}</span>
                    @endif
                </button>
            </nav>
        </div>

        {{-- Contenido de la pestaña Consulta --}}
        @if($activeTab === 'consultation')
            <div class="space-y-6">
                <div>
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-1">
                        Diagnóstico <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        wire:model="diagnosis"
                        id="diagnosis"
                        rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('diagnosis') border-red-500 @enderror"
                        placeholder="Escriba el diagnóstico médico..."
                    ></textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="treatment" class="block text-sm font-medium text-gray-700 mb-1">
                        Tratamiento <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        wire:model="treatment"
                        id="treatment"
                        rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('treatment') border-red-500 @enderror"
                        placeholder="Escriba el tratamiento recomendado..."
                    ></textarea>
                    @error('treatment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas adicionales
                    </label>
                    <textarea
                        wire:model="notes"
                        id="notes"
                        rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Notas adicionales (opcional)..."
                    ></textarea>
                </div>
            </div>
        @endif

        {{-- Contenido de la pestaña Receta --}}
        @if($activeTab === 'prescription')
            <div class="space-y-6">
                {{-- Lista de medicamentos agregados --}}
                @if(count($prescription) > 0)
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-700">Medicamentos en la receta:</h4>
                        @foreach($prescription as $index => $med)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $med['name'] }}</p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Dosis:</span> {{ $med['dosage'] }} |
                                            <span class="font-medium">Frecuencia:</span> {{ $med['frequency'] }} |
                                            <span class="font-medium">Duración:</span> {{ $med['duration'] }}
                                        </p>
                                        @if(!empty($med['instructions']))
                                            <p class="text-sm text-gray-500 mt-1">
                                                <span class="font-medium">Instrucciones:</span> {{ $med['instructions'] }}
                                            </p>
                                        @endif
                                    </div>
                                    <button
                                        wire:click="removeMedication({{ $index }})"
                                        class="text-red-600 hover:text-red-800"
                                        title="Eliminar medicamento"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fa-solid fa-prescription text-4xl mb-3"></i>
                        <p>No hay medicamentos agregados a la receta.</p>
                    </div>
                @endif

                {{-- Formulario para agregar medicamento --}}
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-gray-700 mb-4">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Agregar medicamento
                    </h4>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medicamento *</label>
                            <input
                                type="text"
                                wire:model="newMedication.name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('newMedication.name') border-red-500 @enderror"
                                placeholder="Nombre del medicamento"
                            >
                            @error('newMedication.name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dosis *</label>
                            <input
                                type="text"
                                wire:model="newMedication.dosage"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('newMedication.dosage') border-red-500 @enderror"
                                placeholder="Ej: 500mg"
                            >
                            @error('newMedication.dosage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia *</label>
                            <input
                                type="text"
                                wire:model="newMedication.frequency"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('newMedication.frequency') border-red-500 @enderror"
                                placeholder="Ej: Cada 8 horas"
                            >
                            @error('newMedication.frequency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duración *</label>
                            <input
                                type="text"
                                wire:model="newMedication.duration"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('newMedication.duration') border-red-500 @enderror"
                                placeholder="Ej: 7 días"
                            >
                            @error('newMedication.duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones adicionales</label>
                            <input
                                type="text"
                                wire:model="newMedication.instructions"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej: Tomar con alimentos"
                            >
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-wire-button wire:click="addMedication" blue>
                            <i class="fa-solid fa-plus mr-2"></i>
                            Agregar a la receta
                        </x-wire-button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Botón de guardar --}}
        <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Cancelar
            </a>
            <x-wire-button wire:click="save" blue wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>
                    Guardar Consulta
                </span>
                <span wire:loading wire:target="save">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                    Guardando...
                </span>
            </x-wire-button>
        </div>
    </x-wire-card>

    {{-- Modal de Historial Clínico --}}
    @if($showHistoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeHistoryModal"></div>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                <i class="fa-solid fa-clock-rotate-left mr-2"></i>
                                Consultas Anteriores - {{ $appointment->patient->user->name }}
                            </h3>
                            <button wire:click="closeHistoryModal" class="text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 mb-3">Se muestran hasta las 50 consultas más recientes.</p>
                        <div class="max-h-96 overflow-y-auto">
                            @if(count($patientHistory) > 0)
                                <div class="space-y-4">
                                    @foreach($patientHistory as $consultation)
                                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <span class="text-sm font-medium text-blue-600">
                                                        {{ $consultation->appointment->date->format('d/m/Y') }}
                                                    </span>
                                                    <span class="text-sm text-gray-500 ml-2">
                                                        Dr. {{ $consultation->appointment->doctor->user->name }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <div>
                                                    <span class="text-sm font-medium text-gray-700">Diagnóstico:</span>
                                                    <p class="text-sm text-gray-600">{{ $consultation->diagnosis ?? 'No registrado' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-700">Tratamiento:</span>
                                                    <p class="text-sm text-gray-600">{{ $consultation->treatment ?? 'No registrado' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                                    <p>Este paciente no tiene consultas anteriores registradas.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="closeHistoryModal"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
