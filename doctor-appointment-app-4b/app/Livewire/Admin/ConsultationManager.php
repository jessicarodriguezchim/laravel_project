<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Consultation;
use Livewire\Component;

class ConsultationManager extends Component
{
    public Appointment $appointment;
    public $activeTab = 'consultation';

    // Campos de consulta
    public $diagnosis = '';
    public $treatment = '';
    public $notes = '';

    // Campos de receta (medicamentos)
    public $prescription = [];
    public $newMedication = [
        'name' => '',
        'dosage' => '',
        'frequency' => '',
        'duration' => '',
        'instructions' => '',
    ];

    // Modal de historial
    public $showHistoryModal = false;
    public $patientHistory = [];

    protected $rules = [
        'diagnosis' => 'required|string|min:3',
        'treatment' => 'required|string|min:3',
        'notes' => 'nullable|string',
        'prescription' => 'nullable|array',
        'prescription.*.name' => 'required|string',
        'prescription.*.dosage' => 'required|string',
        'prescription.*.frequency' => 'required|string',
        'prescription.*.duration' => 'required|string',
        'prescription.*.instructions' => 'nullable|string',
    ];

    protected $messages = [
        'diagnosis.required' => 'El diagnóstico es obligatorio.',
        'diagnosis.min' => 'El diagnóstico debe tener al menos 3 caracteres.',
        'treatment.required' => 'El tratamiento es obligatorio.',
        'treatment.min' => 'El tratamiento debe tener al menos 3 caracteres.',
        'prescription.*.name.required' => 'El nombre del medicamento es obligatorio.',
        'prescription.*.dosage.required' => 'La dosis es obligatoria.',
        'prescription.*.frequency.required' => 'La frecuencia es obligatoria.',
        'prescription.*.duration.required' => 'La duración es obligatoria.',
    ];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment->load(['patient.user', 'doctor.user', 'consultation']);

        // Si ya existe una consulta, cargar los datos
        if ($this->appointment->consultation) {
            $this->diagnosis = $this->appointment->consultation->diagnosis ?? '';
            $this->treatment = $this->appointment->consultation->treatment ?? '';
            $this->notes = $this->appointment->consultation->notes ?? '';
            $this->prescription = $this->appointment->consultation->prescription ?? [];
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function addMedication()
    {
        $this->validate([
            'newMedication.name' => 'required|string',
            'newMedication.dosage' => 'required|string',
            'newMedication.frequency' => 'required|string',
            'newMedication.duration' => 'required|string',
        ], [
            'newMedication.name.required' => 'El nombre del medicamento es obligatorio.',
            'newMedication.dosage.required' => 'La dosis es obligatoria.',
            'newMedication.frequency.required' => 'La frecuencia es obligatoria.',
            'newMedication.duration.required' => 'La duración es obligatoria.',
        ]);

        $this->prescription[] = $this->newMedication;

        $this->newMedication = [
            'name' => '',
            'dosage' => '',
            'frequency' => '',
            'duration' => '',
            'instructions' => '',
        ];
    }

    public function removeMedication($index)
    {
        unset($this->prescription[$index]);
        $this->prescription = array_values($this->prescription);
    }

    public function openHistoryModal()
    {
        // Cargar historial de consultas del paciente
        $this->patientHistory = Consultation::whereHas('appointment', function ($query) {
            $query->where('patient_id', $this->appointment->patient_id)
                  ->where('id', '!=', $this->appointment->id);
        })
        ->with(['appointment.doctor.user'])
        ->orderBy('created_at', 'desc')
        ->get();

        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
    }

    public function save()
    {
        $this->validate([
            'diagnosis' => 'required|string|min:3',
            'treatment' => 'required|string|min:3',
            'notes' => 'nullable|string',
        ]);

        $consultation = Consultation::updateOrCreate(
            ['appointment_id' => $this->appointment->id],
            [
                'diagnosis' => $this->diagnosis,
                'treatment' => $this->treatment,
                'notes' => $this->notes,
                'prescription' => $this->prescription,
            ]
        );

        // Marcar la cita como completada
        $this->appointment->update(['status' => Appointment::STATUS_COMPLETED]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Consulta guardada',
            'text' => 'La consulta médica ha sido guardada exitosamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function render()
    {
        return view('livewire.admin.consultation-manager');
    }
}
