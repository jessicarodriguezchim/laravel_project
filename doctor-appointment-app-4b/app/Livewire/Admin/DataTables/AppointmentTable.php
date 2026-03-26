<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()->with([
            'patient' => fn ($q) => $q->select(['id', 'user_id']),
            'patient.user' => fn ($q) => $q->select(['id', 'name']),
            'doctor' => fn ($q) => $q->select(['id', 'user_id']),
            'doctor.user' => fn ($q) => $q->select(['id', 'name']),
        ]);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Paciente", "patient.user.name")
                ->sortable()
                ->searchable(),

            Column::make("Doctor", "doctor.user.name")
                ->sortable()
                ->searchable(),

            Column::make("Fecha", "date")
                ->sortable()
                ->format(function ($value) {
                    return $value->format('d/m/Y');
                }),

            Column::make("Hora inicio", "start_time")
                ->sortable()
                ->format(function ($value) {
                    return date('H:i', strtotime($value));
                }),

            Column::make("Estado", "status")
                ->sortable()
                ->format(function ($value, $row) {
                    $colors = [
                        1 => 'yellow',
                        2 => 'blue',
                        3 => 'green',
                        4 => 'red',
                    ];
                    $names = [
                        1 => 'Pendiente',
                        2 => 'Confirmada',
                        3 => 'Completada',
                        4 => 'Cancelada',
                    ];
                    $color = $colors[$value] ?? 'gray';
                    $name = $names[$value] ?? 'Desconocido';

                    return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-' . $color . '-100 text-' . $color . '-800">' . $name . '</span>';
                })
                ->html(),

            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.appointments.actions', ['appointment' => $row]);
                }),
        ];
    }
}
