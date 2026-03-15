<x-admin-layout title="Atención Médica | Cita #{{ $appointment->id }}"
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Atención Médica']
]">

@livewire('admin.consultation-manager', ['appointment' => $appointment])

</x-admin-layout>
