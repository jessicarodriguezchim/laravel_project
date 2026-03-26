<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Reporte de citas (correo al administrador)
|--------------------------------------------------------------------------
| Prueba facil: everyMinute() — casi siempre que ejecutes "schedule:run" en un minuto nuevo, corre y envia.
| Si usas everyThreeMinutes(), muchas veces veras "No scheduled commands are ready" (normal en minutos 1,2,4,5...).
| Produccion: comenta everyMinute y descomenta dailyAt('08:00') abajo.
|
| Sigue siendo obligatorio en servidor: algo llama cada minuto a php artisan schedule:run
| (schedule-loop.bat / cron). Ver SCHEDULER.md.
*/
Schedule::command('appointments:send-daily-report')
    ->everyThreeMinutes()
    ->timezone(config('app.timezone'));

// Cada 3 minutos (solo minutos :00, :03, :06, :09...): descomenta esto y comenta everyMinute arriba
// Schedule::command('appointments:send-daily-report')
//     ->everyThreeMinutes()
//     ->timezone(config('app.timezone'));

// Produccion (una vez al dia a las 8:00): comenta everyMinute arriba y descomenta esto:
// Schedule::command('appointments:send-daily-report')
//     ->dailyAt('08:00')
//     ->timezone(config('app.timezone'));
