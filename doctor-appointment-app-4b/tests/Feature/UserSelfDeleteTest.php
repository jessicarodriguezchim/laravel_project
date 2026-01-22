<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//vamos a usar la función para refrescar BD
uses(RefreshDatabase::class);

test('un usuario no puede eliminarse a si mismo', function () {
 //1) Crear un usuario de prueba
 $user = User::factory()->create();

 //2) Iniciar sesión como el usuario creado
 $this->actingAs($user, 'web');

 //3) Simular una petición de eliminación HTTP DELETE (borrar usuario)
 $response = $this->delete(route('admin.users.destroy', $user));

 //4) Esperar que el servidor bloquee el borrador a si mismo
 $response->assertStatus(403);

 //5) Verificar que el usuario sigue existiendo en la BD.
 $this->assertDatabaseHas('users', [
    'id' => $user->id, 
    ]);
});