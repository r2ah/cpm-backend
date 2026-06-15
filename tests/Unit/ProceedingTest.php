<?php

use App\Models\Authority;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('does not create a authority without a name field', function () {
    $response = $this->postJson('/api/authorities', []);
    $response->assertStatus(422);
});

it('can create a authority', function () {
    $attributes = Authority::factory()->raw();
    $response = $this->postJson('/api/authorities', $attributes);
    $response->assertStatus(201)->assertJson(['message' => 'Authority has been created']);
    $this->assertDatabaseHas('authorities', $attributes);
});

it('can fetch a authority', function () {
    $item = Authority::factory()->create();

    $response = $this->getJson("/api/authorities/{$item->id}");

    $data = [
        'message' => 'Retrieved Authority',
        'authority' => [
            'id' => $item->id,
            'name' => $item->name,
        ]
    ];

    $response->assertStatus(200)->assertJson($data);
});

it('can update a authority', function () {
    $item = Authority::factory()->create();
    $updatedItem = ['name' => 'Updated Authority'];
    $response = $this->putJson("/api/authorities/{$item->id}", $updatedItem);
    $response->assertStatus(200)->assertJson(['message' => 'Authority has been updated']);
    $this->assertDatabaseHas('authorities', $updatedItem);
});

it('can delete a authority', function () {
    $item = Authority::factory()->create();
    $response = $this->deleteJson("/api/authorities/{$item->id}");
    $response->assertStatus(200)->assertJson(['message' => 'Authority has been deleted']);
    $this->assertCount(0, Authority::all());
});