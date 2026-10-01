<?php

namespace Tests\Feature\Admin;

use App\Enums\ContactStatus;
use App\Enums\QuoteRequestStatus;
use App\Filament\Resources\Contacts\ContactResource;
use App\Filament\Resources\Contacts\Pages\ViewContact;
use App\Filament\Resources\QuoteRequests\Pages\ViewQuoteRequest;
use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Models\Contact;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\User;
use App\Support\LeadLabels;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadViewPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        LeadLabels::flush();
        $this->actingAs(User::factory()->admin()->create());

        Service::create([
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
    }

    public function test_contact_view_shows_readable_labels_and_a_full_width_message(): void
    {
        $contact = Contact::factory()->create([
            'subject' => 'scoping',
            'project_type' => 'mobile-apps',
            'budget_range' => 'Under EGP 100,000',
            'locale' => 'ar',
            'message' => "مرحبًا بكم\nالسطر الثاني <script>alert(1)</script>",
        ]);

        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSee('Project Scoping')
            ->assertDontSee('>scoping<', false)
            ->assertSee('Mobile Applications')
            ->assertDontSee('>mobile-apps<', false)
            ->assertSee('Under EGP 100,000')
            ->assertSee('AR')
            ->assertSee('مرحبًا بكم')
            // direction follows the script of the text, whitespace is not padded in
            ->assertSeeHtml('<div dir="auto" class="lead-message" style="white-space: pre-wrap; text-align: start; overflow-wrap: anywhere;">مرحبًا بكم'."\n".'السطر الثاني &lt;script&gt;alert(1)&lt;/script&gt;</div>')
            ->assertDontSeeHtml('<script>alert(1)</script>');
    }

    public function test_unknown_keys_fall_back_to_the_raw_stored_value(): void
    {
        $contact = Contact::factory()->create([
            'subject' => 'something-new',
            'project_type' => 'منتج رقمي جديد',
        ]);

        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSee('something-new')
            ->assertSee('منتج رقمي جديد');

        $this->assertSame('removed-service', LeadLabels::projectType('removed-service'));
        $this->assertNull(LeadLabels::projectType(null));
        $this->assertSame('New product', LeadLabels::projectType('new-product'));
        $this->assertSame('EN', LeadLabels::locale('en'));
        $this->assertSame('FR', LeadLabels::locale('fr'));
    }

    public function test_quote_request_view_shows_readable_project_type_and_locale_badge(): void
    {
        $quote = QuoteRequest::factory()->create([
            'project_type' => 'mobile-apps',
            'locale' => 'en',
            'description' => "Line one\nLine two",
        ]);

        Livewire::test(ViewQuoteRequest::class, ['record' => $quote->getRouteKey()])
            ->assertSee('Mobile Applications')
            ->assertSee('EN')
            ->assertSeeHtml('<div dir="auto" class="lead-message" style="white-space: pre-wrap; text-align: start; overflow-wrap: anywhere;">Line one'."\n".'Line two</div>');
    }

    public function test_changing_status_persists_and_updates_the_navigation_badge(): void
    {
        $first = Contact::factory()->create(['status' => ContactStatus::New]);
        Contact::factory()->create(['status' => ContactStatus::New]);

        $this->assertSame('2', ContactResource::getNavigationBadge());

        Livewire::test(ViewContact::class, ['record' => $first->getRouteKey()])
            ->callAction('changeStatus', ['status' => ContactStatus::Qualified->value])
            ->assertHasNoActionErrors();

        $this->assertSame(ContactStatus::Qualified, $first->fresh()->status);
        $this->assertSame('1', ContactResource::getNavigationBadge());

        $quote = QuoteRequest::factory()->create(['status' => QuoteRequestStatus::New]);
        $this->assertSame('1', QuoteRequestResource::getNavigationBadge());

        Livewire::test(ViewQuoteRequest::class, ['record' => $quote->getRouteKey()])
            ->callAction('changeStatus', ['status' => QuoteRequestStatus::Won->value])
            ->assertHasNoActionErrors();

        $this->assertSame(QuoteRequestStatus::Won, $quote->fresh()->status);
        $this->assertNull(QuoteRequestResource::getNavigationBadge());
    }
}
