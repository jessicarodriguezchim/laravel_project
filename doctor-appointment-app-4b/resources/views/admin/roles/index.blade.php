<x-admin-layout
    title="Roles | MediConnect"
    :breadcrumbs="[
        ['name' => 'Dashboard',
        'href' => route('admin.dashboard')
        ],
        ['name' => 'Roles',
        'href' => route('admin.roles.index')
        ],
    ]">
    <div class="mb-4 flex justify-end">
        <x-wire-button blue href="{{route('admin.roles.create')}}" >
           <i class="fa-solid fa-plus"></i>
           Nuevo
        </x-wire-button>
    </div>
    @livewire('admin.datatables.role-table')

</x-admin-layout>