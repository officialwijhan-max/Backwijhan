<?php

namespace Tests\Feature;

use App\Mail\ContactClientConfirmation;
use App\Mail\ContactTeamNotification;
use App\Models\Contact;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John Doe',
            'company' => 'Acme Inc',
            'email' => 'john@example.com',
            'phone' => '+201000000000',
            'message' => 'We need a business platform.',
            'locale' => 'en',
        ], $overrides);
    }

    public function test_successful_submission_is_persisted_and_returns_201(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/contact', $this->payload());

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Your request has been received.',
            ])
            ->assertJsonPath('data.status', 'new');

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'new',
        ]);

        $this->assertSame(1, Contact::count());
    }

    public function test_successful_submission_queues_team_and_client_notification_emails(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/contact', $this->payload())->assertStatus(201);

        $contact = Contact::sole();

        Mail::assertQueued(ContactTeamNotification::class, fn ($mail) => $mail->contact->is($contact));
        Mail::assertQueued(ContactClientConfirmation::class, fn ($mail) => $mail->contact->is($contact));
    }

    public function test_missing_name_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/contact', $this->payload(['name' => '']));

        $response->assertStatus(422)
            ->assertJson(['success' => false, 'message' => 'Validation failed.'])
            ->assertJsonValidationErrors('name');

        $this->assertSame(0, Contact::count());
    }

    public function test_invalid_email_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/contact', $this->payload(['email' => 'not-an-email']));

        $response->assertStatus(422)->assertJsonValidationErrors('email');
        $this->assertSame(0, Contact::count());
    }

    public function test_missing_message_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/contact', $this->payload(['message' => '']));

        $response->assertStatus(422)->assertJsonValidationErrors('message');
        $this->assertSame(0, Contact::count());
    }

    private function createService(string $slug, bool $active = true): Service
    {
        return Service::create([
            'number' => '01',
            'slug' => $slug,
            'icon' => 'Code2',
            'title_en' => 'Custom Software',
            'title_ar' => 'الحلول والبرمجيات المخصصة',
            'summary_en' => 'Enterprise applications & platforms',
            'summary_ar' => 'تطبيقات ومنصات مؤسسية',
            'capabilities_en' => [],
            'capabilities_ar' => [],
            'sort_order' => 0,
            'is_active' => $active,
        ]);
    }

    public function test_active_service_slug_is_accepted_as_project_type(): void
    {
        Mail::fake();
        $this->createService('custom-software');

        $this->postJson('/api/v1/contact', $this->payload(['project_type' => 'custom-software']))
            ->assertStatus(201);

        $this->assertDatabaseHas('contacts', ['project_type' => 'custom-software']);
    }

    public function test_fixed_project_type_keys_are_accepted(): void
    {
        Mail::fake();

        foreach (['other', 'not-sure', 'new-product'] as $i => $key) {
            $this->postJson('/api/v1/contact', $this->payload([
                'email' => "fixed{$i}@example.com",
                'project_type' => $key,
            ]))->assertStatus(201);
        }
    }

    public function test_unknown_project_type_is_rejected(): void
    {
        $this->createService('custom-software');

        // A service title (display text) is not an accepted value — only slugs are.
        foreach (['digital-transformation', 'Custom Software', '<script>alert(1)</script>'] as $value) {
            $this->postJson('/api/v1/contact', $this->payload(['project_type' => $value]))
                ->assertStatus(422)
                ->assertJsonValidationErrors('project_type');
        }

        $this->assertSame(0, Contact::count());
    }

    public function test_inactive_service_slug_is_rejected(): void
    {
        $this->createService('legacy-service', active: false);

        $this->postJson('/api/v1/contact', $this->payload(['project_type' => 'legacy-service']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('project_type');
    }

    public function test_over_length_values_are_rejected(): void
    {
        $this->postJson('/api/v1/contact', $this->payload(['project_type' => str_repeat('a', 101)]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('project_type');

        $this->postJson('/api/v1/contact', $this->payload(['budget_range' => str_repeat('a', 101)]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('budget_range');

        $this->postJson('/api/v1/contact', $this->payload(['subject' => str_repeat('a', 151)]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('subject');

        $this->assertSame(0, Contact::count());
    }

    public function test_known_subject_is_accepted_and_unknown_subject_rejected(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/contact', $this->payload(['subject' => 'partnership']))
            ->assertStatus(201);

        $this->postJson('/api/v1/contact', $this->payload([
            'email' => 'other@example.com',
            'subject' => 'Buy cheap links',
        ]))->assertStatus(422)->assertJsonValidationErrors('subject');
    }

    public function test_team_email_escapes_submitted_values(): void
    {
        $contact = Contact::create([
            'name' => '<b>Mallory</b>',
            'email' => 'mallory@example.com',
            'message' => '<script>alert("x")</script>',
            'budget_range' => '<img src=x onerror=alert(1)>',
            'locale' => 'en',
            'status' => 'new',
            'source' => 'website',
            'submitted_at' => now(),
        ]);

        $html = (new ContactTeamNotification($contact))->render();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_rate_limiting_blocks_after_five_requests_per_minute(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/contact', $this->payload(['email' => "user{$i}@example.com"]))
                ->assertStatus(201);
        }

        $this->postJson('/api/v1/contact', $this->payload(['email' => 'sixth@example.com']))
            ->assertStatus(429);
    }
}
