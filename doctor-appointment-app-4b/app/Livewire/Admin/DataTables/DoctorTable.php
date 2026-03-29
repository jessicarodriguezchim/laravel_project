<?php

namespace App\Livewire\Admin\Datatables;

use App\Livewire\Admin\DataTables\BaseDataTable;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DoctorTable extends BaseDataTable
{
    public function builder(): Builder
    {
        return Doctor::query()->with([
            'user' => fn ($q) => $q->select(['id', 'name', 'email']),
            'speciality' => fn ($q) => $q->select(['id', 'name']),
        ]);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable(),

            Column::make('Nombre', 'user.name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'user.email')
                ->sortable()
                ->searchable(),

            Column::make('Especialidad', 'speciality.name')
                ->sortable(),

            Column::make('Licencia', 'license_number')
                ->sortable(),

            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.doctors.actions', ['doctor' => $row]);
                }),
        ];
    }
}
