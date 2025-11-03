<div class="flex items-center space-x-2">
    <x-wire-button href="{{route('admin.roles.edit', $row)}}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    <form action="{{route('admin.roles.destroy',$row)}}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>