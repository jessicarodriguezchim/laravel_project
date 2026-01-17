<x-admin-layout title="Usuarios | Editar" 
:breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Usuarios', 'href' => route('admin.users.index')],
    ['name' => 'Editar']
]">

<x-wire-card>
  <form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="space-y-4">

      <div class="grid lg:grid-cols-2 gap-4">
      <x-wire-input
        name="name"
        label="Nombre"
        :value="old('name', $user->name)"
        required
        placeholder="Nombre"
        autocomplete="name"
      />

      <x-wire-input
        name="email"
        label="Email"
        :value="old('email', $user->email)"
        required
        placeholder="usuario@dominio.com"
        autocomplete="email"
        inputmode="email"
      />

      <x-wire-input
        name="password"
        label="Contraseña (opcional)"
        placeholder="Dejar vacío para mantener la actual"
        autocomplete="new-password"
        type="password"
      />

      <x-wire-input
        name="password_confirmation"
        label="Confirmar Contraseña"
        placeholder="Repita la contraseña si la cambia"
        autocomplete="new-password"
        type="password"
      />

      <x-wire-input
        name="id_number"
        label="Número de ID"
        required :value="old('id_number', $user->id_number)"
        placeholder="Ej. 123456"
        autocomplete="off"
        inputmode="numeric"
      />

      <x-wire-input
        name="phone"
        label="Teléfono"
        required :value="old('phone', $user->phone)"
        placeholder="Ej. 1234567890"
        autocomplete="tel"
        inputmode="tel"
      />
      </div>

      <x-wire-input
        name="address"
        label="Dirección"
        required :value="old('address', $user->address)"
        placeholder="Ej. Calle 123, Ciudad"
        autocomplete="street-address"
      />

      <div>
        <x-wire-native-select name="role_id" label="Rol" required>
          <option value="">Seleccione un rol</option>

          @foreach($roles as $role)
            <option value="{{ $role->id }}" @selected(old('role_id', $user->roles->first()?->id) == $role->id)>
              {{ $role->name }}
            </option>
          @endforeach
        </x-wire-native-select>

        <p class="text-sm text-gray-500">
          Define los permisos y accesos del usuario
        </p>
      </div>

      <div class="flex justify-end space-x-2">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
          Cancelar
        </a>
        <x-wire-button type="submit" blue>
          <i class="fa-solid fa-floppy-disk"></i> Actualizar Usuario
        </x-wire-button>
      </div>

    </div>
  </form>
</x-wire-card>

</x-admin-layout>
