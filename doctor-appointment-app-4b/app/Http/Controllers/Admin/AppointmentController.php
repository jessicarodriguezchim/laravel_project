<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentReceiptMail;
use App\Mail\DoctorAppointmentNotificationMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.appointments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::query()
            ->with(['user' => fn ($q) => $q->select('id', 'name')])
            ->orderBy(
                User::query()->select('name')->whereColumn('id', 'patients.user_id')
            )
            ->get();

        $doctors = Doctor::query()
            ->with(['user' => fn ($q) => $q->select('id', 'name')])
            ->orderBy(
                User::query()->select('name')->whereColumn('id', 'doctors.user_id')
            )
            ->get();

        return view('admin.appointments.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration' => 'nullable|integer|min:5|max:480',
            'reason' => 'required|string|max:1000',
            'status' => 'nullable|integer|in:1,2,3,4',
        ], [
            'patient_id.required' => 'Debe seleccionar un paciente.',
            'patient_id.exists' => 'El paciente seleccionado no existe.',
            'doctor_id.required' => 'Debe seleccionar un doctor.',
            'doctor_id.exists' => 'El doctor seleccionado no existe.',
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha no tiene un formato válido.',
            'date.after_or_equal' => 'La fecha debe ser igual o posterior a hoy.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'start_time.date_format' => 'La hora de inicio no tiene un formato válido.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.date_format' => 'La hora de fin no tiene un formato válido.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'duration.integer' => 'La duración debe ser un número entero.',
            'duration.min' => 'La duración mínima es de 5 minutos.',
            'duration.max' => 'La duración máxima es de 480 minutos (8 horas).',
            'reason.required' => 'El motivo de la consulta es obligatorio.',
            'reason.max' => 'El motivo no debe exceder los 1000 caracteres.',
            'status.in' => 'El estado seleccionado no es válido.',
        ]);

        // Si no se proporciona duración, calcularla automáticamente
        if (empty($data['duration'])) {
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            $data['duration'] = ($end - $start) / 60;
        }

        // Estado por defecto
        if (empty($data['status'])) {
            $data['status'] = Appointment::STATUS_PENDING;
        }

        $appointment = Appointment::create($data);

        $appointment->loadMissing(['patient.user', 'doctor.user']);

        $receiptWasSent = false;
        $doctorNotificationWasSent = false;
        $patientEmail = $appointment->patient?->user?->email;
        $doctorEmail = $appointment->doctor?->user?->email;

        if (! empty($patientEmail)) {
            try {
                $pdf = Pdf::loadView('pdf.appointment-receipt', [
                    'appointment' => $appointment,
                ]);

                $this->sendUsingConfiguredMailers(
                    $patientEmail,
                    new AppointmentReceiptMail($appointment, $pdf->output())
                );

                $receiptWasSent = true;
            } catch (\Throwable $exception) {
                Log::error('No se pudo enviar el comprobante PDF de la cita.', [
                    'appointment_id' => $appointment->id,
                    'patient_email' => $patientEmail,
                    'error' => $exception->getMessage(),
                ]);
            }
        } else {
            Log::warning('No se pudo enviar comprobante: paciente sin correo.', [
                'appointment_id' => $appointment->id,
            ]);
        }

        if (! empty($doctorEmail)) {
            try {
                $this->sendUsingConfiguredMailers(
                    $doctorEmail,
                    new DoctorAppointmentNotificationMail($appointment)
                );

                $doctorNotificationWasSent = true;
            } catch (\Throwable $exception) {
                Log::error('No se pudo enviar la notificacion de nueva cita al doctor.', [
                    'appointment_id' => $appointment->id,
                    'doctor_email' => $doctorEmail,
                    'error' => $exception->getMessage(),
                ]);
            }
        } else {
            Log::warning('No se pudo notificar al doctor: correo no disponible.', [
                'appointment_id' => $appointment->id,
            ]);
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Cita creada',
            'text' => match (true) {
                $receiptWasSent && $doctorNotificationWasSent => 'La cita ha sido registrada y se enviaron los correos al paciente y al doctor.',
                $receiptWasSent => 'La cita ha sido registrada y se envio el comprobante al paciente.',
                $doctorNotificationWasSent => 'La cita ha sido registrada y se envio la notificacion al doctor.',
                default => 'La cita ha sido registrada. No fue posible enviar los correos de notificacion.',
            },
        ]);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'consultation']);
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::query()
            ->with(['user' => fn ($q) => $q->select('id', 'name')])
            ->orderBy(
                User::query()->select('name')->whereColumn('id', 'patients.user_id')
            )
            ->get();

        $doctors = Doctor::query()
            ->with(['user' => fn ($q) => $q->select('id', 'name')])
            ->orderBy(
                User::query()->select('name')->whereColumn('id', 'doctors.user_id')
            )
            ->get();

        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        try {
            $data = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'doctor_id' => 'required|exists:doctors,id',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'duration' => 'nullable|integer|min:5|max:480',
                'reason' => 'required|string|max:1000',
                'status' => 'nullable|integer|in:1,2,3,4',
            ], [
                'patient_id.required' => 'Debe seleccionar un paciente.',
                'patient_id.exists' => 'El paciente seleccionado no existe.',
                'doctor_id.required' => 'Debe seleccionar un doctor.',
                'doctor_id.exists' => 'El doctor seleccionado no existe.',
                'date.required' => 'La fecha es obligatoria.',
                'date.date' => 'La fecha no tiene un formato válido.',
                'date.after_or_equal' => 'La fecha debe ser igual o posterior a hoy.',
                'start_time.required' => 'La hora de inicio es obligatoria.',
                'start_time.date_format' => 'La hora de inicio no tiene un formato válido.',
                'end_time.required' => 'La hora de fin es obligatoria.',
                'end_time.date_format' => 'La hora de fin no tiene un formato válido.',
                'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
                'duration.integer' => 'La duración debe ser un número entero.',
                'duration.min' => 'La duración mínima es de 5 minutos.',
                'duration.max' => 'La duración máxima es de 480 minutos (8 horas).',
                'reason.required' => 'El motivo de la consulta es obligatorio.',
            'reason.max' => 'El motivo no debe exceder los 1000 caracteres.',
                'status.in' => 'El estado seleccionado no es válido.',
            ]);

            // Si no se proporciona duración, calcularla automáticamente
            if (empty($data['duration'])) {
                $start = strtotime($data['start_time']);
                $end = strtotime($data['end_time']);
                $data['duration'] = ($end - $start) / 60;
            }

            $appointment->update($data);

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Cita actualizada',
                'text' => 'La cita ha sido actualizada exitosamente.',
            ]);

            return redirect()->route('admin.appointments.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('flash.banner', 'Error inesperado: No se pudo guardar la información.')
                ->with('flash.bannerStyle', 'danger');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Cita eliminada',
                'text' => 'La cita ha sido eliminada exitosamente.',
            ]);

            return redirect()->route('admin.appointments.index');
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar la cita. Por favor, intenta nuevamente.',
            ]);

            return redirect()->route('admin.appointments.index');
        }
    }

    /**
     * Show the consultation view for an appointment.
     */
    public function consultation(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'consultation']);
        return view('admin.appointments.consultation', compact('appointment'));
    }

    private function sendUsingConfiguredMailers(string $recipient, \Illuminate\Mail\Mailable $mailable): void
    {
        $mailers = [config('mail.default', 'smtp')];

        if (config('mail.send_copy_to_sandbox')) {
            $mailers[] = 'mailtrap_sandbox';
        }

        foreach ($mailers as $mailer) {
            Mail::mailer($mailer)->to($recipient)->send(clone $mailable);
        }
    }
}
