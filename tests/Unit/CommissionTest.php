<?php

use App\Models\Commission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not create a commission without a name field', function () {
    $response = $this->postJson('/api/commissions', []);
    $response->assertStatus(422);
});

it('can create a commission', function () {
    $attributes = Commission::factory()->raw();
    $response = $this->postJson('/api/commissions', $attributes);
    $response->assertStatus(201)->assertJson(['message' => 'Commission has been created']);
    $this->assertDatabaseHas('commissions', $attributes);
});

it('can fetch a commission', function () {
    $item = Commission::factory()->create();

    $response = $this->getJson("/api/commissions/{$item->id}");

    $data = [
        'message' => 'Retrieved Commission',
        'commission' => [
            'id' => $item->id,
            'name' => $item->name,
            'email' => $item->email,
            'level' => $item->level,
            'region' => $item->region,
            'parent' => $item->parent,
            'members' => $item->members
        ]
    ];

    $response->assertStatus(200)->assertJson($data);
});

it('can update a commission', function () {
    $item = Commission::factory()->create();
    $updatedItem = ['name' => 'Updated Commission'];
    $response = $this->putJson("/api/commissions/{$item->id}", $updatedItem);
    $response->assertStatus(200)->assertJson(['message' => 'Commission has been updated']);
    $this->assertDatabaseHas('commission', $updatedItem);
});

it('can delete a commission', function () {
    $item = Commission::factory()->create();
    $response = $this->deleteJson("/api/commissions/{$item->id}");
    $response->assertStatus(200)->assertJson(['message' => 'Commission has been deleted']);
    $this->assertCount(0, Commission::all());
});