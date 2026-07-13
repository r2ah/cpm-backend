<?php

use App\Models\Opinion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not create a opinion without a name field', function () {
    $response = $this->postJson('/api/opinions', []);
    $response->assertStatus(422);
});

it('can create a opinion', function () {
    $attributes = Opinion::factory()->raw();
    $response = $this->postJson('/api/opinions', $attributes);
    $response->assertStatus(201)->assertJson(['message' => 'Opinion has been created']);
    $this->assertDatabaseHas('opinions', $attributes);
});

it('can fetch a opinion', function () {
    $item = Opinion::factory()->create();

    $response = $this->getJson("/api/opinions/{$item->id}");

    $data = [
        'message' => 'Retrieved Opinion',
        'authority' => [
            'id' => $item->id,
            'name' => $item->name,
        ]
    ];

    $response->assertStatus(200)->assertJson($data);
});

it('can update a opinion', function () {
    $item = Opinion::factory()->create();
    $updatedItem = ['name' => 'Updated Opinion'];
    $response = $this->putJson("/api/opinions/{$item->id}", $updatedItem);
    $response->assertStatus(200)->assertJson(['message' => 'Opinion has been updated']);
    $this->assertDatabaseHas('opinions', $updatedItem);
});

it('can delete a authority', function () {
    $item = Opinion::factory()->create();
    $response = $this->deleteJson("/api/opinions/{$item->id}");
    $response->assertStatus(200)->assertJson(['message' => 'Opinion has been deleted']);
    $this->assertCount(0, Opinion::all());
});