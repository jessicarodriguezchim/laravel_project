<?php

namespace App\Imports;

use App\Imports\Concerns\ResolvesPatientSpreadsheetRow;
use App\Models\User;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

/**
 * Reads the spreadsheet like {@see PatientsImport} and rejects uploads that repeat
 * emails or document numbers within the file or that already exist in the database.
 */
class PatientsImportDuplicateScanner implements OnEachRow, SkipsEmptyRows, WithHeadingRow
{
    use ResolvesPatientSpreadsheetRow;

    public ?string $abortReason = null;

    /** @var array<string, true> */
    private array $emailsSeen = [];

    /** @var array<string, true> */
    private array $documentsSeen = [];

    public function onRow(Row $row): void
    {
        if ($this->abortReason !== null) {
            return;
        }

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

        $idFromFile = $this->first($row, [
            'id_number', 'identificacion', 'cedula', 'documento',
            'numero_de_id', 'numero_id', 'numero_de_identificacion', 'dni',
            'nro_documento', 'no_documento', 'num_documento', 'numero_documento', 'curp', 'rfc',
        ]) ?? $this->firstByHeaderSubstring($row, [
            'document', 'cedula', 'identif', 'dni', 'curp', 'rfc', 'nit',
        ], ['emergency', 'emergencia', 'contacto_emergencia', 'parentesco']);

        if (! $name || ! $email) {
            return;
        }

        $email = strtolower(trim($email));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->abortReason = __('The spreadsheet contains an invalid email address: :email.', ['email' => $email]);

            return;
        }

        if (isset($this->emailsSeen[$email])) {
            $this->abortReason = __('The same email appears more than once in the file: :email.', ['email' => $email]);

            return;
        }
        $this->emailsSeen[$email] = true;

        if ($idFromFile !== null && trim($idFromFile) !== '') {
            $doc = trim($idFromFile);
            if (isset($this->documentsSeen[$doc])) {
                $this->abortReason = __('The same ID or document number appears more than once in the file: :doc.', ['doc' => $doc]);

                return;
            }
            $this->documentsSeen[$doc] = true;
        }
    }

    public function conflictMessage(): ?string
    {
        if ($this->abortReason !== null) {
            return $this->abortReason;
        }

        $emails = array_keys($this->emailsSeen);
        if ($emails === []) {
            return null;
        }

        $existingEmails = User::query()
            ->whereIn('email', $emails)
            ->pluck('email');
        if ($existingEmails->isNotEmpty()) {
            return __('These emails are already registered: :emails.', [
                'emails' => $this->formatSampleList($existingEmails->all()),
            ]);
        }

        $documents = array_keys($this->documentsSeen);
        if ($documents !== []) {
            $existingDocs = User::query()
                ->whereIn('id_number', $documents)
                ->pluck('id_number');
            if ($existingDocs->isNotEmpty()) {
                return __('These ID or document numbers are already registered: :ids.', [
                    'ids' => $this->formatSampleList($existingDocs->all()),
                ]);
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $values
     */
    private function formatSampleList(array $values): string
    {
        $max = 12;
        $slice = array_slice($values, 0, $max);
        $text = implode(', ', $slice);
        if (count($values) > $max) {
            $text .= ' '.__('(:count more)', ['count' => count($values) - $max]);
        }

        return $text;
    }
}
