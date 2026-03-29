<?php

namespace App\Jobs;

use App\Imports\PatientsImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessPatientsImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public string $path,
        public string $importId
    ) {}

    public function handle(): void
    {
        try {
            Excel::import(new PatientsImport($this->importId), $this->path, 'local');

            $data = Cache::get($this->importId, ['current' => 0, 'total' => 1]);
            $data['status'] = 'finished';
            $data['current'] = $data['total'];
            Cache::put($this->importId, $data, 3600);
        } catch (\Throwable $e) {
            Log::error('patients.import.failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $data = Cache::get($this->importId, ['current' => 0, 'total' => 1]);
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            Cache::put($this->importId, $data, 3600);
        } finally {
            Storage::disk('local')->delete($this->path);
        }
    }
}
