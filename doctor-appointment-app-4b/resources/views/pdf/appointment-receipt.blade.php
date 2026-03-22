<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de cita</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 14px; }
        .container { padding: 24px; }
        .title { font-size: 22px; margin-bottom: 8px; }
        .subtitle { margin-bottom: 18px; color: #666; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { border: 1px solid #ddd; padding: 8px; }
        .label { background-color: #f7f7f7; width: 35%; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Comprobante de cita médica</div>
        <div class="subtitle">Folio #{{ $appointment->id }}</div>

        <table class="table">
            <tr>
                <td class="label">Paciente</td>
                <td>{{ $appointment->patient?->user?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Correo del paciente</td>
                <td>{{ $appointment->patient?->user?->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Doctor</td>
                <td>{{ $appointment->doctor?->user?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Fecha</td>
                <td>{{ optional($appointment->date)->format('Y-m-d') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Hora</td>
                <td>{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
            </tr>
            <tr>
                <td class="label">Duracion</td>
                <td>{{ $appointment->duration }} minutos</td>
            </tr>
            <tr>
                <td class="label">Motivo</td>
                <td>{{ $appointment->reason }}</td>
            </tr>
            <tr>
                <td class="label">Estado</td>
                <td>{{ $appointment->status_name }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
