<?php

namespace Tests\Feature;

use App\Mail\QuoteRequestClientConfirmation;
use App\Mail\QuoteRequestTeamNotification;
use App\Models\QuoteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Roe',
            'company' => 'Acme Inc',
            'email' => 'jane@example.com',
            'phone' => '+201000000000',
            'project_type' => 'New digital product',
            'budget_range' => 'Under $10,000',
            'description' => 'We need a business platform.',
            'locale' => 'en',
        ], $overrides);
    }

    public function test_successful_submission_is_persisted_and_returns_201(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/quote-requests', $this->payload());

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Your request has been received.'])
            ->assertJsonPath('data.status', 'new');

        $this->assertDatabaseHas('quote_requests', [
            'name' => 'Jane Roe',
            'email' => 'jane@example.com',
            'project_type' => 'New digital product',
            'status' => 'new',
        ]);

        $this->assertSame(1, QuoteRequest::count());
    }

    public function test_successful_submission_queues_team_and_client_notification_emails(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/quote-requests', $this->payload())->assertStatus(201);

        $quoteRequest = QuoteRequest::sole();

        Mail::assertQueued(QuoteRequestTeamNotification::class, fn ($mail) => $mail->quoteRequest->is($quoteRequest));
        Mail::assertQueued(QuoteRequestClientConfirmation::class, fn ($mail) => $mail->quoteRequest->is($quoteRequest));
    }

    public function test_invalid_email_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/quote-requests', $this->payload(['email' => 'not-an-email']));

        $response->assertStatus(422)->assertJsonValidationErrors('email');
        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_missing_project_type_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/quote-requests', $this->payload(['project_type' => '']));

        $response->assertStatus(422)->assertJsonValidationErrors('project_type');
        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_missing_budget_range_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/quote-requests', $this->payload(['budget_range' => '']));

        $response->assertStatus(422)->assertJsonValidationErrors('budget_range');
        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_missing_description_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/quote-requests', $this->payload(['description' => '']));

        $response->assertStatus(422)->assertJsonValidationErrors('description');
        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_arabic_option_values_are_accepted(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/quote-requests', $this->payload([
            'project_type' => 'منتج رقمي جديد',
            'budget_range' => 'أقل من 10,000 دولار',
            'locale' => 'ar',
        ]));

        $response->assertStatus(201);
        $this->assertDatabaseHas('quote_requests', ['locale' => 'ar', 'project_type' => 'منتج رقمي جديد']);
    }
}
