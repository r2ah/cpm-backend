<?php

use App\Models\Intervention;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('does not create a intervention without a name field', function () {
    $response = $this->postJson('/api/interventions', []);
    $response->assertStatus(422);
});

it('can create a intervention', function () {
    $attributes = Intervention::factory()->raw();
    $response = $this->postJson('/api/interventions', $attributes);
    $response->assertStatus(201)->assertJson(['message' => 'Intervention has been created']);
    $this->assertDatabaseHas('interventions', $attributes);
});

it('can fetch a authority', function () {
    $item = Intervention::factory()->create();

    $response = $this->getJson("/api/interventions/{$item->id}");

    $data = [
        'message' => 'Retrieved Intervention',
        'intervention' => [
            'id' => $item->id,
            'name' => $item->name,
            'parent' => $item->parent,
        ]
    ];

    $response->assertStatus(200)->assertJson($data);
});

it('can update a intervention', function () {
    $item = Intervention::factory()->create();
    $updatedItem = ['name' => 'Updated Intervention'];
    $response = $this->putJson("/api/interventions/{$item->id}", $updatedItem);
    $response->assertStatus(200)->assertJson(['message' => 'Intervention has been updated']);
    $this->assertDatabaseHas('interventions', $updatedItem);
});

it('can delete a intervention', function () {
    $item = Intervention::factory()->create();
    $response = $this->deleteJson("/api/interventions/{$item->id}");
    $response->assertStatus(200)->assertJson(['message' => 'Intervention has been deleted']);
    $this->assertCount(0, Intervention::all());
});