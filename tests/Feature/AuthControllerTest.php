<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── register ──────────────────────────────────────────────────────────────

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'username'              => 'johndoe',
            'email'                 => 'john@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user'         => ['id', 'name', 'username', 'email'],
                'access_token',
                'token_type',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_register_fails_when_email_already_taken(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'username'              => 'johndoe',
            'email'                 => 'john@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_when_username_already_taken(): void
    {
        User::factory()->create(['username' => 'johndoe']);

        $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'username'              => 'johndoe',
            'email'                 => 'new@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['username']);
    }

    public function test_register_fails_when_required_fields_missing(): void
    {
        $this->postJson('/api/auth/register', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'username', 'email', 'password']);
    }

    // ── login ─────────────────────────────────────────────────────────────────

    public function test_login_returns_token_for_valid_credentials(): void
    {
        User::factory()->create([
            'email'    => 'jane@example.com',
            'password' => bcrypt('Password1!'),
        ]);

        $this->postJson('/api/auth/login', [
            'email'    => 'jane@example.com',
            'password' => 'Password1!',
        ])->assertOk()
          ->assertJsonStructure([
              'user'         => ['id', 'name', 'email'],
              'access_token',
              'token_type',
          ]);
    }

    public function test_login_returns_401_for_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->postJson('/api/auth/login', [
            'email'    => 'jane@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized()
          ->assertJson(['message' => 'Invalid credentials.']);
    }

    public function test_login_fails_when_required_fields_missing(): void
    {
        $this->postJson('/api/auth/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    // ── logout ────────────────────────────────────────────────────────────────

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJson(['message' => 'Successfully logged out.']);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/auth/logout')
            ->assertUnauthorized();
    }
}
