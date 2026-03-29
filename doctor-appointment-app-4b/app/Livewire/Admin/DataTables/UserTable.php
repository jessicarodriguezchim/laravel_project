<?php

namespace App\Livewire\Admin\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UserTable extends BaseDataTable
{
    // Se comenta para personalizar consultas
    // protected $model = User::class;

    // Dfine el modelo y su consulta
    public function builder(): Builder
    {
        return User::query()->with([
            'roles',
            'doctor' => fn ($q) => $q->select(['id', 'user_id']),
            'patient' => fn ($q) => $q->select(['id', 'user_id']),
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
            Column::make('Name', 'name')
                ->sortable(),
            Column::make('Email', 'email')
                ->sortable(),
            Column::make('Número de id', 'id_number')
                ->sortable(),
            Column::make('Teléfono', 'phone')
                ->sortable(),
            Column::make('Rol', 'roles')
                ->label(function ($row) {
                    return $row->roles->first()?->name ?? 'Sin Rol';
                }),
            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.users.actions',
                        ['user' => $row]);
                }),

        ];
    }
}
