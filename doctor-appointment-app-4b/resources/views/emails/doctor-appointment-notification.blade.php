<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva cita asignada</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2>Nueva cita asignada</h2>

    <p>Hola Dr(a). {{ $appointment->doctor?->user?->name ?? 'doctor' }},</p>

    <p>Se ha agendado una nueva cita en el sistema.</p>

    <ul>
        <li><strong>Folio:</strong> #{{ $appointment->id }}</li>
        <li><strong>Paciente:</strong> {{ $appointment->patient?->user?->name ?? 'N/A' }}</li>
        <li><strong>Correo del paciente:</strong> {{ $appointment->patient?->user?->email ?? 'N/A' }}</li>
        <li><strong>Fecha:</strong> {{ optional($appointment->date)->format('Y-m-d') ?? 'N/A' }}</li>
        <li><strong>Hora:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</li>
        <li><strong>Motivo:</strong> {{ $appointment->reason }}</li>
    </ul>
</body>
</html>
