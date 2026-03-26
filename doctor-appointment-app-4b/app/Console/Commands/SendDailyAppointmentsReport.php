<?php

namespace App\Console\Commands;

use App\Mail\DailyAppointmentsReportMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyAppointmentsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia al administrador el reporte de citas agendadas para hoy.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $adminEmails = config('mail.admin_report_addresses', []);
        $adminEmails = is_array($adminEmails) ? $adminEmails : [];

        if (empty($adminEmails)) {
            $this->error('No hay correos configurados para el reporte diario (ADMIN_REPORT_EMAILS o ADMIN_REPORT_EMAIL).');
            return self::FAILURE;
        }

        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('date', $today->toDateString())
            ->orderBy('start_time')
            ->get();

        try {
            $this->sendUsingConfiguredMailers(
                $adminEmails,
                new DailyAppointmentsReportMail(
                    appointments: $appointments,
                    reportDate: $today->format('Y-m-d')
                )
            );
        } catch (\Throwable $exception) {
            Log::error('No se pudo enviar el reporte diario de citas.', [
                'admin_emails' => $adminEmails,
                'error' => $exception->getMessage(),
            ]);

            $this->error('Fallo al enviar el reporte diario: '.$exception->getMessage());
            return self::FAILURE;
        }

        Log::info('Recordatorio / reporte diario: correo enviado por SMTP.', [
            'to' => $adminEmails,
            'appointments_today' => $appointments->count(),
            'report_date' => $today->format('Y-m-d'),
        ]);

        $this->info('Reporte enviado a '.implode(', ', $adminEmails).' con '.$appointments->count().' cita(s).');
        return self::SUCCESS;
    }

    private function sendUsingConfiguredMailers(array $recipients, Mailable $mailable): void
    {
        // Usa el mailer por defecto de .env (MAIL_MAILER), no solo "smtp_real".
        $mailers = [config('mail.default', 'smtp')];

        if (config('mail.send_copy_to_sandbox')) {
            $mailers[] = 'mailtrap_sandbox';
        }

        foreach ($mailers as $mailer) {
            Mail::mailer($mailer)->to($recipients)->send(clone $mailable);
        }
    }
}
