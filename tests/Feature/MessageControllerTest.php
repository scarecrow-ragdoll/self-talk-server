<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── helpers ───────────────────────────────────────────────────────────────

    /**
     * Create a room and attach a user as a member.
     */
    private function createRoomWithMember(?User $owner = null): array
    {
        $owner = $owner ?? User::factory()->create();
        $room  = Room::factory()->create(['created_by' => $owner->id]);
        $owner->rooms()->attach($room->id, ['role' => 'admin', 'joined_at' => now()]);

        return [$room, $owner];
    }

    // ── index ─────────────────────────────────────────────────────────────────

    public function test_index_returns_paginated_messages_for_member(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;
        [$room] = $this->createRoomWithMember($user);
        Message::factory()->count(3)->create(['room_id' => $room->id, 'user_id' => $user->id]);

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->getJson("/api/rooms/{$room->id}/messages")
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'body', 'room_id', 'user_id',
                        'user' => ['id', 'name', 'username'],
                    ],
                ],
                'current_page', 'per_page', 'total',
            ]);
    }

    public function test_index_returns_403_for_non_member(): void
    {
        [$room] = $this->createRoomWithMember();
        $nonMember = User::factory()->create();
        $plainTextToken = $nonMember->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->getJson("/api/rooms/{$room->id}/messages")
            ->assertForbidden();
    }

    public function test_index_requires_authentication(): void
    {
        [$room] = $this->createRoomWithMember();

        $this->getJson("/api/rooms/{$room->id}/messages")
            ->assertUnauthorized();
    }

    public function test_index_returns_404_for_nonexistent_room(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->getJson('/api/rooms/99999/messages')
            ->assertNotFound();
    }

    // ── store ─────────────────────────────────────────────────────────────────

    public function test_store_creates_message_for_member(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;
        [$room, $user] = $this->createRoomWithMember($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson("/api/rooms/{$room->id}/messages", [
                'body' => 'Hello, world!',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'body', 'room_id', 'user_id',
                'user' => ['id', 'name', 'username'],
            ])
            ->assertJsonPath('body', 'Hello, world!')
            ->assertJsonPath('user_id', $user->id);

        $this->assertDatabaseHas('messages', [
            'room_id' => $room->id,
            'user_id' => $user->id,
            'body'    => 'Hello, world!',
        ]);
    }

    public function test_store_returns_403_for_non_member(): void
    {
        [$room] = $this->createRoomWithMember();
        $nonMember = User::factory()->create();
        $plainTextToken = $nonMember->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson("/api/rooms/{$room->id}/messages", ['body' => 'Hi!'])
            ->assertForbidden();
    }

    public function test_store_fails_when_body_is_missing(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;
        [$room] = $this->createRoomWithMember($user);

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson("/api/rooms/{$room->id}/messages", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    public function test_store_requires_authentication(): void
    {
        [$room] = $this->createRoomWithMember();

        $this->postJson("/api/rooms/{$room->id}/messages", ['body' => 'Hi!'])
            ->assertUnauthorized();
    }

    public function test_store_returns_404_for_nonexistent_room(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson('/api/rooms/99999/messages', ['body' => 'Hi!'])
            ->assertNotFound();
    }
}
