<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_list_of_users(): void
    {
        $user = User::factory()->create();
        User::factory()->count(4)->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(5)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'username', 'email', 'created_at'],
            ]);
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/users')
            ->assertUnauthorized();
    }
}
