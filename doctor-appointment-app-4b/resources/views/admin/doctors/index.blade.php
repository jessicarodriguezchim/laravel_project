<x-admin-layout title="Doctores | Healthify" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Doctores'],
]">
    <x-slot name="actions">
        <x-wire-button href="{{ route('admin.doctors.create') }}" blue>
            <i class="fa-solid fa-plus mr-2"></i>
            Nuevo Doctor
        </x-wire-button>
    </x-slot>
    @livewire('admin.datatables.doctor-table')
</x-admin-layout>

