<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Hello there.',
        ], $overrides);
    }

    public function test_contact_budget_range_is_constrained_to_an_allowlist(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/contact', $this->payload(['budget_range' => '<script>alert(1)</script>']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('budget_range');

        foreach (['Under EGP 100,000', 'أقل من 100,000 جنيه', 'Under $10,000'] as $i => $value) {
            $this->postJson('/api/v1/contact', $this->payload([
                'email' => "ok{$i}@example.com",
                'budget_range' => $value,
            ]))->assertStatus(201);
        }
    }

    public function test_security_headers_are_present_on_api_responses(): void
    {
        $this->getJson('/api/v1/pricing')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer');
    }

    public function test_admin_endpoints_reject_unauthenticated_requests(): void
    {
        $this->getJson('/api/v1/admin/contacts')->assertStatus(401);
        $this->getJson('/api/v1/admin/quote-requests')->assertStatus(401);
        $this->getJson('/api/v1/admin/dashboard')->assertStatus(401);
        $this->patchJson('/api/v1/admin/contacts/1', ['status' => 'spam'])->assertStatus(401);
        $this->deleteJson('/api/v1/admin/contacts/1')->assertStatus(401);
    }

    public function test_team_email_subject_strips_control_characters(): void
    {
        $contact = Contact::create([
            'name' => "Mallory\r\nBcc: evil@example.com",
            'email' => 'mallory@example.com',
            'message' => 'x',
            'locale' => 'en',
            'status' => 'new',
            'source' => 'website',
            'submitted_at' => now(),
        ]);

        $subject = (new \App\Mail\ContactTeamNotification($contact))->envelope()->subject;

        $this->assertStringNotContainsString("\n", $subject);
        $this->assertStringNotContainsString("\r", $subject);
    }

    public function test_public_read_endpoints_are_rate_limited(): void
    {
        for ($i = 0; $i < 120; $i++) {
            $this->getJson('/api/v1/pricing')->assertOk();
        }

        $this->getJson('/api/v1/pricing')->assertStatus(429);
    }
}
