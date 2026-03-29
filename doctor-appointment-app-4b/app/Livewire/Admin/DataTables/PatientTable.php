<?php

namespace App\Livewire\Admin\Datatables;

use App\Livewire\Admin\DataTables\BaseDataTable;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PatientTable extends BaseDataTable
{
    // protected $model = Patient::class;

    public function builder(): Builder
    {
        return Patient::query()->with(['user']);
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
                ->sortable(),

            Column::make('Email', 'user.email')
                ->sortable(),

            Column::make('Número de id', 'user.id_number')
                ->sortable(),

            Column::make('Teléfono', 'user.phone')
                ->sortable(),

            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.patients.actions',
                        ['patient' => $row]);
                }),
        ];
    }
}
