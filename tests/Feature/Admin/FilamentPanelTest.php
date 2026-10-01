<?php

namespace Tests\Feature\Admin;

use App\Enums\ContactStatus;
use App\Enums\QuoteRequestStatus;
use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Filament\Resources\CaseStudies\Pages\CreateCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\EditCaseStudy;
use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\Contacts\Pages\ViewContact;
use App\Filament\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Filament\Resources\QuoteRequests\Pages\ViewQuoteRequest;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Widgets\LatestContacts;
use App\Filament\Widgets\LatestQuoteRequests;
use App\Filament\Widgets\LeadStatsOverview;
use App\Filament\Widgets\SubmissionsChart;
use App\Models\CaseStudy;
use App\Models\Contact;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_panel_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/contacts')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_active_admin_can_log_in_through_the_panel(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'correct-password']);

        Livewire::test(Login::class)
            ->fillForm(['email' => $admin->email, 'password' => 'correct-password'])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'correct-password']);

        Livewire::test(Login::class)
            ->fillForm(['email' => $admin->email, 'password' => 'nope'])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_access_the_panel(): void
    {
        $this->actingAs(User::factory()->inactive()->create());

        $this->get('/admin')->assertForbidden();
    }

    public function test_dashboard_and_resource_pages_load_for_an_admin(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        foreach (['/admin', '/admin/contacts', '/admin/quote-requests', '/admin/services', '/admin/services/create', '/admin/case-studies', '/admin/case-studies/create'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_contacts_list_is_empty_and_loads(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(ListContacts::class)
            ->assertSuccessful()
            ->assertCountTableRecords(0);
    }

    public function test_contacts_list_search_and_filters(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $ann = Contact::factory()->create(['name' => 'Ann Smith', 'company' => 'Acme', 'subject' => 'call', 'status' => ContactStatus::New, 'submitted_at' => '2026-01-10 10:00:00']);
        $bob = Contact::factory()->create(['name' => 'Bob Jones', 'email' => 'bob@example.org', 'subject' => 'support', 'status' => ContactStatus::Closed, 'submitted_at' => '2026-03-10 10:00:00']);

        Livewire::test(ListContacts::class)
            ->assertCanSeeTableRecords([$ann, $bob])
            ->searchTable('Acme')
            ->assertCanSeeTableRecords([$ann])->assertCanNotSeeTableRecords([$bob])
            ->searchTable('bob@example.org')
            ->assertCanSeeTableRecords([$bob])->assertCanNotSeeTableRecords([$ann])
            ->searchTable('')
            ->filterTable('status', [ContactStatus::Closed->value])
            ->assertCanSeeTableRecords([$bob])->assertCanNotSeeTableRecords([$ann])
            ->resetTableFilters()
            ->filterTable('subject', 'call')
            ->assertCanSeeTableRecords([$ann])->assertCanNotSeeTableRecords([$bob])
            ->resetTableFilters()
            ->filterTable('submitted_at', ['from' => '2026-02-01', 'until' => '2026-04-01'])
            ->assertCanSeeTableRecords([$bob])->assertCanNotSeeTableRecords([$ann]);
    }

    public function test_contact_view_page_shows_message_and_status_can_be_changed(): void
    {
        $this->actingAs(User::factory()->create()); // manager

        $contact = Contact::factory()->create(['message' => 'A very specific project brief.', 'status' => ContactStatus::New]);

        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSee('A very specific project brief.')
            ->callAction('changeStatus', ['status' => ContactStatus::Contacted->value])
            ->assertHasNoActionErrors();

        $this->assertSame(ContactStatus::Contacted, $contact->fresh()->status);
    }

    public function test_invalid_status_is_rejected_by_the_shared_validation_rules(): void
    {
        $this->actingAs(User::factory()->create());
        $contact = Contact::factory()->create(['status' => ContactStatus::New]);

        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->callAction('changeStatus', ['status' => 'bogus'])
            ->assertHasActionErrors(['status']);

        $this->assertSame(ContactStatus::New, $contact->fresh()->status);
    }

    public function test_quote_request_list_and_status_change(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $quote = QuoteRequest::factory()->create(['status' => QuoteRequestStatus::New]);

        Livewire::test(ListQuoteRequests::class)->assertCanSeeTableRecords([$quote]);

        Livewire::test(ViewQuoteRequest::class, ['record' => $quote->getRouteKey()])
            ->assertSee($quote->description)
            ->callAction('changeStatus', ['status' => QuoteRequestStatus::ProposalSent->value])
            ->assertHasNoActionErrors();

        $this->assertSame(QuoteRequestStatus::ProposalSent, $quote->fresh()->status);
    }

    public function test_manager_cannot_delete_but_admin_can(): void
    {
        $contact = Contact::factory()->create();

        $this->actingAs(User::factory()->create()); // manager
        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertActionHidden('delete');

        $this->actingAs(User::factory()->admin()->create());
        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->callAction('delete');

        $this->assertModelMissing($contact);
    }

    public function test_contacts_cannot_be_created_from_the_panel(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/contacts/create')->assertNotFound();
    }

    public function test_service_can_be_created_with_validation(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreateService::class)
            ->fillForm([
                'number' => '07',
                'slug' => 'Not A Slug',
                'icon' => 'code',
                'title_en' => 'Title',
                'title_ar' => 'عنوان',
                'summary_en' => 'Summary',
                'summary_ar' => 'ملخص',
                'capabilities_en' => [['capability' => 'One']],
                'capabilities_ar' => [['capability' => 'واحد']],
                'sort_order' => 3,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        Livewire::test(CreateService::class)
            ->fillForm([
                'number' => '07',
                'slug' => 'new-service',
                'icon' => 'code',
                'title_en' => 'Title',
                'title_ar' => 'عنوان',
                'summary_en' => 'Summary',
                'summary_ar' => 'ملخص',
                'capabilities_en' => [['capability' => 'One']],
                'capabilities_ar' => [['capability' => 'واحد']],
                'sort_order' => 3,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $service = Service::where('slug', 'new-service')->firstOrFail();
        $this->assertSame(['One'], $service->capabilities_en);
    }

    public function test_services_list_loads(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $service = Service::create([
            'number' => '01', 'slug' => 's', 'icon' => 'code', 'title_en' => 'T', 'title_ar' => 'ت',
            'summary_en' => 'S', 'summary_ar' => 'م', 'capabilities_en' => ['a'], 'capabilities_ar' => ['أ'],
        ]);

        Livewire::test(ListServices::class)->assertCanSeeTableRecords([$service]);
    }

    public function test_case_study_create_with_nested_content_and_auto_published_at(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreateCaseStudy::class)
            ->fillForm([
                'slug' => 'demo-project',
                'title_en' => 'Demo',
                'title_ar' => 'تجريبي',
                'summary_en' => 'Sum',
                'summary_ar' => 'ملخص',
                'display_order' => 0,
                'is_published' => true,
                'client_visibility' => true,
                'sections' => [['type' => 'challenge', 'title_en' => 'The challenge', 'content_en' => 'Text']],
                'technologies' => [['name' => 'Flutter', 'category' => 'Mobile']],
                'contributions' => [['name_en' => 'Backend', 'name_ar' => 'الخلفية']],
                'features' => [['title_en' => 'Feature', 'title_ar' => 'ميزة']],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $study = CaseStudy::where('slug', 'demo-project')->firstOrFail();
        $this->assertNotNull($study->published_at);
        $this->assertCount(1, $study->sections);
        $this->assertSame('Flutter', $study->technologies->first()->name);
        $this->assertCount(1, $study->contributions);
        $this->assertCount(1, $study->features);

        Livewire::test(EditCaseStudy::class, ['record' => $study->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet(['title_en' => 'Demo']);

        $this->assertNotNull(CaseStudyResource::getUrl('index'));
    }

    public function test_dashboard_widgets_render(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Contact::factory()->count(2)->create(['status' => ContactStatus::New, 'submitted_at' => now()]);
        QuoteRequest::factory()->create(['status' => QuoteRequestStatus::New, 'submitted_at' => now()]);

        Livewire::test(LeadStatsOverview::class)->assertSee('New contact requests')->assertSee('New quote requests');
        Livewire::test(SubmissionsChart::class)->assertSuccessful();
        Livewire::test(LatestContacts::class)->assertCountTableRecords(2);
        Livewire::test(LatestQuoteRequests::class)->assertCountTableRecords(1);
    }
}
