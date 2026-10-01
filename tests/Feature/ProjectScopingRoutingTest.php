<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\QuoteRequest;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The frontend sends the Project Scoping forms to /quote-requests and every
 * other inquiry tab (call, partnership, support) to /contact. These tests pin
 * the payloads those forms really post and where each one must end up.
 */
class ProjectScopingRoutingTest extends TestCase
{
    use RefreshDatabase;

    private function service(string $slug = 'custom-software', bool $active = true): Service
    {
        return Service::create([
            'number' => '01',
            'slug' => $slug,
            'icon' => 'Code2',
            'title_en' => 'Custom Software',
            'title_ar' => 'الحلول والبرمجيات المخصصة',
            'summary_en' => 'S',
            'summary_ar' => 'م',
            'capabilities_en' => [],
            'capabilities_ar' => [],
            'sort_order' => 0,
            'is_active' => $active,
        ]);
    }

    /** What src/lib/inquiry-request.ts posts for a Project Scoping submission. */
    private function scopingPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Roe',
            'company' => 'Acme Inc',
            'email' => 'jane@example.com',
            'phone' => '+201000000000',
            'project_type' => 'custom-software',
            'budget_range' => 'EGP 100,000 – 250,000',
            'description' => "Project requirements summary:\nMain goal: Launch a new product\n\nWe need a platform.",
            'locale' => 'en',
        ], $overrides);
    }

    /** What the Schedule a Call / Partnership / Support tabs post. */
    private function contactPayload(string $subject, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Roe',
            'email' => 'jane@example.com',
            'subject' => $subject,
            'project_type' => 'custom-software',
            'message' => 'Hello',
            'locale' => 'en',
        ], $overrides);
    }

    public function test_scoping_payload_is_stored_as_a_quote_request_and_not_a_contact(): void
    {
        Mail::fake();
        $this->service();

        $this->postJson('/api/v1/quote-requests', $this->scopingPayload())
            ->assertCreated()
            ->assertJsonPath('data.status', 'new');

        $this->assertSame(0, Contact::count());
        $this->assertDatabaseHas('quote_requests', [
            'email' => 'jane@example.com',
            'project_type' => 'custom-software',
            'budget_range' => 'EGP 100,000 – 250,000',
            'locale' => 'en',
        ]);
    }

    public function test_scoping_budget_is_optional_and_stored_as_null(): void
    {
        Mail::fake();
        $this->service();

        $payload = $this->scopingPayload();
        unset($payload['budget_range']);

        $this->postJson('/api/v1/quote-requests', $payload)->assertCreated();

        $this->assertNull(QuoteRequest::sole()->budget_range);
    }

    public function test_scoping_accepts_the_not_sure_option_and_arabic_budget_labels(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/quote-requests', $this->scopingPayload([
            'project_type' => 'not-sure',
            'budget_range' => 'غير متأكد — أحتاج استشارة',
            'description' => 'نُريدُ تَطبيقًا 🚀',
            'locale' => 'ar',
        ]))->assertCreated();

        $quote = QuoteRequest::sole();
        $this->assertSame('ar', $quote->locale);
        $this->assertSame('نُريدُ تَطبيقًا 🚀', $quote->description);
    }

    public function test_scoping_rejects_an_inactive_service_slug(): void
    {
        $this->service('retired', active: false);

        $this->postJson('/api/v1/quote-requests', $this->scopingPayload(['project_type' => 'retired']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('project_type');

        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_other_inquiry_tabs_are_stored_as_contacts_and_not_quote_requests(): void
    {
        Mail::fake();
        $this->service();

        foreach (['call', 'partnership', 'support'] as $subject) {
            $this->postJson('/api/v1/contact', $this->contactPayload($subject))->assertCreated();
        }

        $this->assertSame(3, Contact::count());
        $this->assertSame(0, QuoteRequest::count());
        $this->assertSame(['call', 'partnership', 'support'], Contact::orderBy('id')->pluck('subject')->all());
    }

    public function test_scoping_validation_errors_are_localized_to_arabic(): void
    {
        $this->service();

        $this->postJson('/api/v1/quote-requests', $this->scopingPayload([
            'description' => '',
            'email' => 'not-an-email',
            'locale' => 'ar',
        ]))
            ->assertStatus(422)
            ->assertJsonPath('message', 'تعذّر التحقق من البيانات المُدخلة. يرجى مراجعة الحقول.')
            ->assertJsonPath('errors.description.0', 'الحقل وصف المشروع مطلوب.')
            ->assertJsonPath('errors.email.0', 'يرجى إدخال بريد إلكتروني صالح في الحقل البريد الإلكتروني.');

        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_quote_requests_share_the_lead_capture_throttle_with_contact(): void
    {
        Mail::fake();
        $this->service();

        foreach (range(1, 3) as $i) {
            $this->postJson('/api/v1/contact', $this->contactPayload('call'))->assertCreated();
        }
        foreach (range(1, 2) as $i) {
            $this->postJson('/api/v1/quote-requests', $this->scopingPayload())->assertCreated();
        }

        $this->postJson('/api/v1/quote-requests', $this->scopingPayload(['locale' => 'ar']))
            ->assertStatus(429)
            ->assertJsonPath('message', 'عدد الطلبات كبير. يرجى المحاولة لاحقًا.');

        $this->assertSame(2, QuoteRequest::count());
    }
}
