<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_admin_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create(['password' => 'correct-password']);

        $response = $this->postJson('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertStatus(403)->assertJson(['success' => false]);
    }

    public function test_inactive_admin_cannot_access_protected_apis_even_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Deactivated after the token was issued — the token itself is
        // still structurally valid, so the active-admin check must be
        // enforced on every request, not only at login.
        $user->update(['is_active' => false]);

        $response = $this->getJson('/api/v1/admin/dashboard', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403)->assertJson(['success' => false]);
    }

    public function test_manager_cannot_delete_a_contact(): void
    {
        $manager = User::factory()->create();
        $token = $manager->createToken('test')->plainTextToken;
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/v1/admin/contacts/{$contact->id}", [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);
    }

    public function test_admin_can_delete_a_contact(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test')->plainTextToken;
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/v1/admin/contacts/{$contact->id}", [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
