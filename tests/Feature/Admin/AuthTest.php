<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_returns_a_token(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);

        $response = $this->postJson('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'email', 'role']]]);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);

        $response = $this->postJson('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)->assertJson(['success' => false]);
    }

    public function test_authenticated_admin_endpoint_is_reachable_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/v1/admin/me', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)->assertJsonPath('data.id', $user->id);
    }

    public function test_unauthenticated_admin_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/admin/me');

        $response->assertStatus(401)->assertJson(['success' => false]);
    }
}
