<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('no se puede actualizar un usuario con datos inválidos', function () {

    $user = User::factory()->create();
    $this->actingAs($user);

    // Agregar header Accept: application/json para que Laravel devuelva 422 en lugar de redirigir
    $response = $this->putJson(
        route('admin.users.update', $user),
        ['name' => ''] // inválido
    );

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'email', 'id_number', 'phone', 'address', 'role_id']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => $user->name,
    ]);
});
