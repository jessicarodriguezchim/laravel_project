<x-admin-layout title="Usuarios | MediConnect"
    :breadcrumbs="[
        ['name' => 'Dashboard',
        'href' => route('admin.dashboard')
        ],
        ['name' => 'Usuarios',
        'href' => route('admin.users.index')
        ],
        ['name' => 'Editar'
        ]
    ]">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <x-label for="name" value="Nombre" />
                <x-input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="mt-1 block w-full" 
                    placeholder="Nombre del usuario"
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus
                />
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <x-label for="email" value="Correo" />
                <x-input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="mt-1 block w-full" 
                    placeholder="correo@ejemplo.com"
                    value="{{ old('email', $user->email) }}" 
                    required
                />
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <x-label for="id_number" value="Número de ID" />
                <x-input 
                    id="id_number" 
                    name="id_number" 
                    type="text" 
                    class="mt-1 block w-full" 
                    placeholder="Número de identificación"
                    value="{{ old('id_number', $user->id_number) }}"
                />
                @error('id_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <x-label for="phone" value="Teléfono" />
                <x-input 
                    id="phone" 
                    name="phone" 
                    type="text" 
                    class="mt-1 block w-full" 
                    placeholder="Número de teléfono"
                    value="{{ old('phone', $user->phone) }}"
                />
                @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <x-label for="role_id" value="Rol" />
                <select 
                    id="role_id" 
                    name="role_id" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    required
                >
                    <option value="">Seleccione un rol</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ (old('role_id', $user->roles->first()?->id) == $role->id) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-4 space-x-2">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
