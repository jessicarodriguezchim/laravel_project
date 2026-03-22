<div class="flex items-center space-x-2">

    @if($user->hasRole('Doctor') && $user->doctor)
        {{-- Si es Doctor y tiene registro, ir a editar Doctor --}}
        <x-wire-button href="{{ route('admin.doctors.edit', $user->doctor) }}" blue xs>
            <i class="fa-solid fa-user-doctor"></i>
        </x-wire-button>
    @elseif($user->hasRole('Paciente') && $user->patient)
        {{-- Si es Paciente y tiene registro, ir a editar Paciente --}}
        <x-wire-button href="{{ route('admin.patients.edit', $user->patient) }}" blue xs>
            <i class="fa-solid fa-user-injured"></i>
        </x-wire-button>
    @else
        {{-- Si no, editar usuario normal --}}
    <x-wire-button href="{{ route('admin.users.edit', $user) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    @endif

    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>

</div>