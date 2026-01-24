<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('usuario no autenticado no puede actualizar un usuario', function () {

    $user = User::factory()->create();

    $response = $this->put(
        route('admin.users.update', $user),
        ['name' => 'Nuevo Nombre']
    );

    // En aplicaciones web, Laravel redirige (302) a login en lugar de devolver 401
    $response->assertStatus(302);
    $response->assertRedirect(); // Verifica que es una redirección

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => $user->name,
    ]);
});
