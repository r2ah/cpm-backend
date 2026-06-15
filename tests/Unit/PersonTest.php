<?php

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('does not create a person without a name field', function () {
    $response = $this->postJson('/api/people', []);
    $response->assertStatus(422);
});

it('can create a person', function () {
    $attributes = Person::factory()->raw();
    $response = $this->postJson('/api/people', $attributes);
    $response->assertStatus(201)->assertJson(['message' => 'Person has been created']);
    $this->assertDatabaseHas('people', $attributes);
});

it('can fetch a person', function () {
    $item = Person::factory()->create();

    $response = $this->getJson("/api/people/{$item->id}");

    $data = [
        'message' => 'Retrieved Person',
        'person' => [
            'id' => $item->id,
            'name' => $item->name,
            'email' => $item->email,
            'phone' => $item->phone,
            'is_natural_person' => $item->is_natural_person
        ]
    ];

    $response->assertStatus(200)->assertJson($data);
});

it('can update a person', function () {
    $item = Person::factory()->create();
    $updatedItem = ['name' => 'Updated Person'];
    $response = $this->putJson("/api/people/{$item->id}", $updatedItem);
    $response->assertStatus(200)->assertJson(['message' => 'Person has been updated']);
    $this->assertDatabaseHas('people', $updatedItem);
});

it('can delete a person', function () {
    $item = Person::factory()->create();
    $response = $this->deleteJson("/api/people/{$item->id}");
    $response->assertStatus(200)->assertJson(['message' => 'Person has been deleted']);
    $this->assertCount(0, Person::all());
});