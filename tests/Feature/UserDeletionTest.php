<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create([
        'name' => 'admin',
        'guard_name' => 'api',
    ]);
});

it('prevents an admin from deleting their own account', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $response = $this->deleteJson("/api/v1/users/{$admin->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('allows an admin to delete another admin when an admin remains', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $targetAdmin = User::factory()->create();
    $targetAdmin->assignRole('admin');
    Sanctum::actingAs($admin);

    $response = $this->deleteJson("/api/v1/users/{$targetAdmin->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('users', ['id' => $targetAdmin->id]);
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
