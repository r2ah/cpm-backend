<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not create a user without a name field', function () {
    $response = $this->postJson('/api/users', []);
    $response->assertStatus(422);
});

it('can create a user', function () {
    $attributes = User::factory()->raw();
    $response = $this->postJson('/api/users', $attributes);
    $response->assertStatus(201)->assertJson(['message' => 'User has been created']);
    $this->assertDatabaseHas('users', $attributes);
});

it('can fetch a user', function () {
    $item = User::factory()->create();

    $response = $this->getJson("/api/users/{$item->id}");

    $data = [
        'message' => 'Retrieved User',
        'user' => [
            'id' => $item->id,
            'name' => $item->name,
            'email' => $item->email
        ]
    ];

    $response->assertStatus(200)->assertJson($data);
});

it('can update a user', function () {
    $item = User::factory()->create();
    $updatedItem = ['name' => 'Updated User'];
    $response = $this->putJson("/api/users/{$item->id}", $updatedItem);
    $response->assertStatus(200)->assertJson(['message' => 'User has been updated']);
    $this->assertDatabaseHas('users', $updatedItem);
});

it('can delete a user', function () {
    $item = User::factory()->create();
    $response = $this->deleteJson("/api/users/{$item->id}");
    $response->assertStatus(200)->assertJson(['message' => 'User has been deleted']);
    $this->assertCount(0, User::all());
});