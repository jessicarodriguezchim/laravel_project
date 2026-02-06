<x-admin-layout title="Pacientes | Healthify" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Pacientes'],
]">
    <x-slot name="actions">
    </x-slot>
    @livewire('admin.datatables.patient-table')
</x-admin-layout>