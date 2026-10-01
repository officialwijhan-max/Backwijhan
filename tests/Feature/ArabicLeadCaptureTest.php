<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Arabic submissions end to end at the API boundary: stored intact, answered
 * in Arabic, rate limited with a clear 429, and cheap to preflight.
 */
class ArabicLeadCaptureTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'عبد العزيز جمال',
            'company' => 'وجهان',
            'email' => 'client@example.com',
            'message' => 'مرحبًا بكم 👋',
            'subject' => 'scoping',
            'locale' => 'ar',
        ], $overrides);
    }

    public function test_arabic_message_with_diacritics_and_emoji_is_stored_intact_with_locale_ar(): void
    {
        Mail::fake();

        $message = "السَّلامُ عَلَيْكُمْ، نُريدُ تَطبيقًا جَديدًا 🚀\nالسطر الثاني: ١٢٣ و 456 ✅";

        $this->postJson('/api/v1/contact', $this->payload(['message' => $message]))
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'استلمنا طلبك وسنتواصل معك قريبًا.');

        $contact = Contact::firstOrFail();
        $this->assertSame('ar', $contact->locale);
        $this->assertSame($message, $contact->message);
        $this->assertSame('عبد العزيز جمال', $contact->name);
        $this->assertTrue(mb_check_encoding($contact->message, 'UTF-8'));
    }

    public function test_validation_errors_are_returned_in_arabic_for_locale_ar(): void
    {
        $response = $this->postJson('/api/v1/contact', $this->payload(['email' => 'not-an-email', 'message' => '']))
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'تعذّر التحقق من البيانات المُدخلة. يرجى مراجعة الحقول.');

        $response->assertJsonPath('errors.email.0', 'يرجى إدخال بريد إلكتروني صالح في الحقل البريد الإلكتروني.');
        $response->assertJsonPath('errors.message.0', 'الحقل الرسالة مطلوب.');
        $this->assertSame(0, Contact::count());
    }

    public function test_accept_language_header_selects_arabic_when_no_locale_field_is_sent(): void
    {
        $this->postJson('/api/v1/contact', ['email' => 'bad'], ['Accept-Language' => 'ar-EG,ar;q=0.9,en;q=0.5'])
            ->assertStatus(422)
            ->assertJsonPath('errors.name.0', 'الحقل الاسم مطلوب.');
    }

    public function test_english_stays_english_and_unknown_locales_fall_back(): void
    {
        $this->postJson('/api/v1/contact', ['email' => 'bad', 'locale' => 'en'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonPath('errors.name.0', 'The name field is required.');

        // `locale` is validated by the request, but the middleware must never
        // load an arbitrary lang file from it either.
        $this->postJson('/api/v1/contact', ['email' => 'bad', 'locale' => '../../etc'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Validation failed.');
    }

    public function test_the_sixth_submission_in_a_minute_is_throttled_with_a_localized_429(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/v1/contact', $this->payload(['message' => "رسالة {$i}"]))->assertCreated();
        }

        $this->postJson('/api/v1/contact', $this->payload(['message' => 'السادسة']))
            ->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'عدد الطلبات كبير. يرجى المحاولة لاحقًا.');

        $this->assertSame(5, Contact::count());
    }

    public function test_throttled_responses_carry_a_valid_retry_after_header(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/v1/contact', $this->payload())->assertCreated();
        }

        $response = $this->postJson('/api/v1/contact', $this->payload())->assertStatus(429);

        // Message stays localized; the header is additive.
        $response->assertJsonPath('message', 'عدد الطلبات كبير. يرجى المحاولة لاحقًا.');

        $retryAfter = $response->headers->get('Retry-After');
        $this->assertNotNull($retryAfter, 'Retry-After header is missing on 429');
        $this->assertMatchesRegularExpression('/^\d+$/', $retryAfter);
        $this->assertGreaterThanOrEqual(1, (int) $retryAfter);
        $this->assertLessThanOrEqual(60, (int) $retryAfter, 'lead-capture window is one minute');
        $this->assertSame('0', $response->headers->get('X-RateLimit-Remaining'));
    }

    public function test_the_throttle_message_is_english_by_default(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/v1/contact', $this->payload(['locale' => 'en']))->assertCreated();
        }

        $this->postJson('/api/v1/contact', $this->payload(['locale' => 'en']))
            ->assertStatus(429)
            ->assertJsonPath('message', 'Too many requests. Please try again later.');
    }

    public function test_cors_preflight_is_cached_by_the_browser_for_a_day(): void
    {
        config(['cors.allowed_origins' => ['http://localhost:8080']]);

        $response = $this->call('OPTIONS', '/api/v1/contact', [], [], [], [
            'HTTP_ORIGIN' => 'http://localhost:8080',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
            'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'content-type',
        ]);

        $response->assertNoContent();
        $this->assertSame('86400', $response->headers->get('Access-Control-Max-Age'));
        $this->assertSame('http://localhost:8080', $response->headers->get('Access-Control-Allow-Origin'));
    }

    public function test_cors_does_not_allow_unlisted_origins_or_a_wildcard(): void
    {
        config(['cors.allowed_origins' => ['http://localhost:8080']]);

        $response = $this->call('OPTIONS', '/api/v1/contact', [], [], [], [
            'HTTP_ORIGIN' => 'https://evil.example',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
        ]);

        // With a single allowed origin the CORS layer always answers with that origin;
        // the browser then refuses the mismatch. What must never happen is echoing the caller back.
        $this->assertNotSame('https://evil.example', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertNotSame('*', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertNotSame('*', config('cors.allowed_origins')[0]);
        $this->assertFalse(config('cors.supports_credentials'));
    }

    public function test_public_api_does_not_start_a_session(): void
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertOk();
        $this->assertEmpty($response->headers->getCookies(), 'public API responses must not set cookies');
    }

    public function test_service_edits_show_up_immediately_despite_the_response_cache(): void
    {
        $service = Service::create([
            'number' => '01',
            'slug' => 'mobile-apps',
            'icon' => 'smartphone',
            'title_en' => 'Mobile Applications',
            'title_ar' => 'تطبيقات الموبايل',
            'summary_en' => 'S',
            'summary_ar' => 'م',
            'capabilities_en' => ['a'],
            'capabilities_ar' => ['أ'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/services')->assertJsonPath('data.0.title', 'Mobile Applications');

        // Served from cache the second time: a raw query-builder write bypasses model events.
        Service::query()->whereKey($service->id)->toBase()->update(['title_en' => 'Changed behind the cache']);
        $this->getJson('/api/v1/services')->assertJsonPath('data.0.title', 'Mobile Applications');

        // An admin edit goes through the model, which flushes the cache.
        $service->update(['title_en' => 'Mobile Apps']);
        $this->getJson('/api/v1/services')->assertJsonPath('data.0.title', 'Mobile Apps');

        $service->delete();
        $this->getJson('/api/v1/services')->assertJsonCount(0, 'data');
    }
}
