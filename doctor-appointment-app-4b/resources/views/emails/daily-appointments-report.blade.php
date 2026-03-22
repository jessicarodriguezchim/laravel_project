<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte diario de citas</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2>Reporte diario de citas</h2>
    <p><strong>Fecha del reporte:</strong> {{ $reportDate }}</p>

    @if($appointments->isEmpty())
        <p>No hay citas agendadas para hoy.</p>
    @else
        <p>Listado de pacientes agendados:</p>
        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">
            <thead style="background-color: #f3f4f6;">
                <tr>
                    <th align="left">Paciente</th>
                    <th align="left">Correo</th>
                    <th align="left">Doctor</th>
                    <th align="left">Hora</th>
                    <th align="left">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->patient?->user?->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->patient?->user?->email ?? 'N/A' }}</td>
                        <td>{{ $appointment->doctor?->user?->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                        <td>{{ $appointment->status_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
