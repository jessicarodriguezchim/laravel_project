<?php

namespace App\Livewire\Admin\DataTables;

use App\Models\Role;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RoleTable extends BaseDataTable
{
    protected $model = Role::class;

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
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Fecha', 'created_at')
                ->sortable()
                ->format(function ($value) {
                    if (is_null($value)) {
                        return '-';
                    }

                    return $value->format('d/m/Y');
                }),
            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.roles.actions',
                        ['row' => $row]);
                }),
        ];
    }
}
