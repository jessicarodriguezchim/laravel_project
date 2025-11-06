<x-admin-layout title="Roles | MediConnect"
    :breadcrumbs="[
        ['name' => 'Dashboard',
        'href' => route('admin.dashboard')
        ],
        ['name' => 'Roles',
        'href' => route('admin.roles.index')
        ],
        ['name' => 'Nuevo'
        ]
    ]">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <x-label for="name" value="Nombre" />
                    <x-input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="mt-1 block w-full" 
                        placeholder="Nombre del rol"
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                    />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

</x-admin-layout>