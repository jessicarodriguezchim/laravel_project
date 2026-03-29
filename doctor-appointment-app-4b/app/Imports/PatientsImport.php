<?php

namespace App\Imports;

use App\Imports\Concerns\ResolvesPatientSpreadsheetRow;
use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;

class PatientsImport implements OnEachRow, SkipsEmptyRows, WithChunkReading, WithEvents, WithHeadingRow
{
    use ResolvesPatientSpreadsheetRow;

    protected int $cachedTotal = 1;

    protected int $totalProcessed = 0;

    protected int $totalImported = 0;

    protected int $totalSkipped = 0;

    protected int $rowsSinceFlush = 0;

    protected int $flushEvery = 50;

    protected bool $loggedSkipKeys = false;

    protected bool $loggedPlaceholderIds = false;

    public function __construct(
        public readonly string $importId
    ) {}

    public function chunkSize(): int
    {
        return 500;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $reader = $event->reader;
                if (! method_exists($reader, 'getTotalRows')) {
                    return;
                }
                $totals = $reader->getTotalRows();
                $sum = is_array($totals) ? array_sum($totals) : (int) $totals;
                $this->cachedTotal = $sum > 0 ? $sum : 1;

                $data = Cache::get($this->importId, [
                    'current' => 0,
                    'total' => 1,
                    'status' => 'processing',
                    'imported' => 0,
                    'skipped' => 0,
                ]);
                $data['total'] = $this->cachedTotal;
                Cache::put($this->importId, $data, 3600);

                // Update cache often on small sheets so the progress bar does not look stuck.
                $this->flushEvery = $this->cachedTotal <= 100 ? 1 : 50;
            },
            AfterImport::class => function () {
                $this->flushProgress(true);
            },
        ];
    }

    public function onRow(Row $row): void
    {
        // Raw values: avoids Excel turning long numbers into scientific notation or dates.
        $row = $this->mergeHeadingAliases($row->toArray(null, false, false));

        $baseName = $this->first($row, ['name', 'nombre', 'nombre_completo', 'full_name'])
            ?? $this->firstByHeaderSubstring($row, ['nombre', 'name', 'paciente'], []);
        $surname = $this->first($row, [
            'apellido', 'apellidos', 'last_name', 'surname', 'primer_apellido', 'segundo_apellido',
            'apellido_paterno', 'apellido_materno',
        ]);
        $name = $this->joinNameParts([$baseName, $surname]);

        $email = $this->first($row, ['email', 'correo', 'correo_electronico', 'e_mail', 'mail'])
            ?? $this->firstByHeaderSubstring($row, ['email', 'correo', 'mail'], ['emergency', 'emergencia'])
            ?? $this->guessEmailFromRow($row);
        $idNumber = $this->first($row, [
            'id_number', 'identificacion', 'cedula', 'documento',
            'numero_de_id', 'numero_id', 'numero_de_identificacion', 'dni',
            'nro_documento', 'no_documento', 'num_documento', 'numero_documento', 'curp', 'rfc',
        ]) ?? $this->firstByHeaderSubstring($row, [
            'document', 'cedula', 'identif', 'dni', 'curp', 'rfc', 'nit',
        ], ['emergency', 'emergencia', 'contacto_emergencia', 'parentesco']);
        $phone = $this->first($row, [
            'phone', 'telefono', 'tel', 'telefono_movil', 'celular', 'movil', 'numero_de_telefono',
            'telefono_celular', 'whatsapp',
        ]) ?? $this->firstByHeaderSubstring($row, [
            'telefono', 'tel', 'celular', 'movil', 'phone', 'whatsapp',
        ], ['emergency', 'emergencia', 'contacto_emergencia', 'parentesco']);
        $address = $this->first($row, ['address', 'direccion', 'domicilio']) ?? '—';

        $usedGeneratedId = false;
        if ($idNumber === null || $idNumber === '') {
            $idNumber = $this->generatedIdNumberForRow($row);
            $usedGeneratedId = true;
        }

        $usedPlaceholderPhone = false;
        if ($phone === null || $phone === '') {
            $phone = '0000000000';
            $usedPlaceholderPhone = true;
        }

        $this->totalProcessed++;

        if ($usedGeneratedId || $usedPlaceholderPhone) {
            if (! $this->loggedPlaceholderIds) {
                $this->loggedPlaceholderIds = true;
                Log::info('patients.import.placeholder_id_or_phone', [
                    'import_id' => $this->importId,
                    'generated_id' => $usedGeneratedId,
                    'placeholder_phone' => $usedPlaceholderPhone,
                    'hint' => 'Add document and phone columns to the spreadsheet when you have real data.',
                ]);
            }
        }

        if (! $name || ! $email) {
            $this->totalSkipped++;
            if (! $this->loggedSkipKeys) {
                $this->loggedSkipKeys = true;
                Log::info('patients.import.row_skipped_missing_fields', [
                    'import_id' => $this->importId,
                    'heading_keys' => array_values(array_filter(array_keys($row), is_string(...))),
                ]);
            }
        } else {
            $email = strtolower(trim($email));
            if (User::where('email', $email)->exists() || User::where('id_number', $idNumber)->exists()) {
                $this->totalSkipped++;
            } else {
                $bloodTypeId = null;
                $bloodLabel = $this->first($row, ['blood_type', 'tipo_sangre', 'blood']);
                if ($bloodLabel) {
                    $bloodLabel = trim((string) $bloodLabel);
                    $bt = BloodType::query()->where('name', $bloodLabel)->first();
                    $bloodTypeId = $bt?->id;
                }

                $emergencyPhone = $this->first($row, ['emergency_contact_phone', 'telefono_emergencia', 'contacto_emergencia_tel']);
                if ($emergencyPhone !== null && $emergencyPhone !== '') {
                    $emergencyPhone = preg_replace('/\D/', '', (string) $emergencyPhone);
                    $emergencyPhone = strlen($emergencyPhone) === 10 ? $emergencyPhone : null;
                } else {
                    $emergencyPhone = null;
                }

                try {
                    DB::transaction(function () use ($name, $email, $idNumber, $phone, $address, $bloodTypeId, $emergencyPhone, $row) {
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'password' => config('app.import_default_password'),
                            'id_number' => $idNumber,
                            'phone' => preg_replace('/\D/', '', (string) $phone) ?: (string) $phone,
                            'address' => $address,
                        ]);
                        $user->assignRole('Paciente');
                        $user->email_verified_at = now();
                        $user->save();

                        Patient::create([
                            'user_id' => $user->id,
                            'blood_type_id' => $bloodTypeId,
                            'allergies' => $this->first($row, ['allergies', 'alergias']),
                            'chronic_conditions' => $this->first($row, ['chronic_conditions', 'condiciones_cronicas', 'cronicas']),
                            'surgical_history' => $this->first($row, ['surgical_history', 'cirugias', 'historial_quirurgico']),
                            'family_history' => $this->first($row, ['family_history', 'historial_familiar']),
                            'observations' => $this->first($row, ['observations', 'observaciones', 'notas']),
                            'emergency_contact_name' => $this->first($row, ['emergency_contact_name', 'contacto_emergencia', 'nombre_emergencia']),
                            'emergency_contact_phone' => $emergencyPhone,
                            'emergency_contact_relationship' => $this->first($row, ['emergency_contact_relationship', 'parentesco_emergencia']),
                        ]);
                    });
                    $this->totalImported++;
                } catch (\Throwable $e) {
                    $this->totalSkipped++;
                    Log::warning('patients.import.row_failed', [
                        'email' => $email,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->rowsSinceFlush++;
        if ($this->rowsSinceFlush >= $this->flushEvery) {
            $this->flushProgress(false);
            $this->rowsSinceFlush = 0;
        }
    }

    protected function flushProgress(bool $final): void
    {
        $data = Cache::get($this->importId, [
            'current' => 0,
            'total' => $this->cachedTotal,
            'status' => 'processing',
            'imported' => 0,
            'skipped' => 0,
        ]);
        $data['current'] = $this->totalProcessed;
        $data['total'] = max($data['total'], $this->totalProcessed, 1);
        $data['imported'] = $this->totalImported;
        $data['skipped'] = $this->totalSkipped;
        if (! $final) {
            $data['status'] = 'processing';
        }
        Cache::put($this->importId, $data, 3600);
    }

    /**
     * @param  array<int|string, mixed>  $row
     */
    private function generatedIdNumberForRow(array $row): string
    {
        $payload = $this->importId."\0".serialize($row);

        return 'IMP-'.substr(hash('sha256', $payload), 0, 24);
    }
}
