<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('usuario autenticado puede eliminar otro usuario', function () {

    $admin = User::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    $response = $this->delete(route('admin.users.destroy', $user));

    $response->assertRedirect();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
