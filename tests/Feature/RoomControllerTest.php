<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── index ─────────────────────────────────────────────────────────────────

    public function test_index_returns_only_rooms_the_user_belongs_to(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        // Two rooms the user is a member of
        $room1 = Room::factory()->create(['created_by' => $user->id]);
        $room2 = Room::factory()->create(['created_by' => $other->id]);
        $user->rooms()->attach($room1->id, ['role' => 'admin',  'joined_at' => now()]);
        $user->rooms()->attach($room2->id, ['role' => 'member', 'joined_at' => now()]);

        // A room the user does NOT belong to
        Room::factory()->create(['created_by' => $other->id]);

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id', 'name', 'type',
                    'creator' => ['id', 'name', 'username'],
                    'users'   => [['id', 'name', 'username']],
                ],
            ]);
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/rooms')->assertUnauthorized();
    }

    // ── store ─────────────────────────────────────────────────────────────────

    public function test_store_creates_room_and_attaches_creator_as_admin(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson('/api/rooms', [
                'name' => 'My Room',
                'type' => 'group',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'name', 'type',
                'creator' => ['id', 'name', 'username'],
                'users'   => [['id', 'name', 'username']],
            ])
            ->assertJsonPath('name', 'My Room')
            ->assertJsonPath('type', 'group');

        $this->assertDatabaseHas('rooms', ['name' => 'My Room', 'created_by' => $user->id]);

        // Creator must be attached as admin
        $this->assertDatabaseHas('room_user', [
            'room_id' => $response->json('id'),
            'user_id' => $user->id,
            'role'    => 'admin',
        ]);
    }

    public function test_store_uses_group_type_by_default(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson('/api/rooms', ['name' => 'No Type Room'])
            ->assertStatus(201);

        $this->assertDatabaseHas('rooms', ['name' => 'No Type Room']);
    }

    public function test_store_fails_when_name_is_missing(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson('/api/rooms', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_fails_when_type_is_invalid(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson('/api/rooms', ['name' => 'Room', 'type' => 'invalid'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/rooms', ['name' => 'Room'])->assertUnauthorized();
    }
}
