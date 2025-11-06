<div class="flex items-center space-x-2">
    <x-wire-button href="{{route('admin.roles.edit', $row)}}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    <form action="{{route('admin.roles.destroy', $row)}}" method="POST" class="inline" id="delete-form-{{ $row->id }}">
        @csrf
        @method('DELETE')
        <button type="button" onclick="confirmDeleteRole({{ $row->id }}, '{{ addslashes($row->name) }}')" class="inline-flex items-center px-2 py-1 bg-red-600 border border-transparent rounded font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fa-solid fa-trash"></i>
        </button>
    </form>
</div>

@once
<script>
if (typeof window.confirmDeleteRole === 'undefined') {
    window.confirmDeleteRole = function(id, name) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Estás seguro de que deseas eliminar el rol "' + name + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        } else {
            if (confirm('¿Estás seguro de que deseas eliminar el rol "' + name + '"?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    };
}
</script>
@endonce