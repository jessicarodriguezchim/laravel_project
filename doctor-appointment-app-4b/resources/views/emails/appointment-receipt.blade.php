<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de cita</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2>Comprobante de cita médica</h2>

    <p>Hola {{ $appointment->patient?->user?->name ?? 'paciente' }},</p>

    <p>Tu cita fue registrada correctamente. En este correo se adjunta tu comprobante en formato PDF.</p>

    <ul>
        <li><strong>Folio:</strong> #{{ $appointment->id }}</li>
        <li><strong>Fecha:</strong> {{ optional($appointment->date)->format('Y-m-d') ?? 'N/A' }}</li>
        <li><strong>Hora:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</li>
        <li><strong>Doctor:</strong> {{ $appointment->doctor?->user?->name ?? 'N/A' }}</li>
    </ul>

    <p>Gracias por confiar en nosotros.</p>
</body>
</html>
