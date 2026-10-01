<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactStatus;
use App\Enums\QuoteRequestStatus;
use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\QuoteRequest;

class DashboardController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $newContacts = Contact::where('status', ContactStatus::New)->count();
        $newQuoteRequests = QuoteRequest::where('status', QuoteRequestStatus::New)->count();

        $openContacts = Contact::whereNotIn('status', [ContactStatus::Closed, ContactStatus::Spam])->count();
        $openQuoteRequests = QuoteRequest::whereNotIn('status', [
            QuoteRequestStatus::Won, QuoteRequestStatus::Lost, QuoteRequestStatus::Spam,
        ])->count();

        $qualifiedContacts = Contact::where('status', ContactStatus::Qualified)->count();
        $qualifiedQuoteRequests = QuoteRequest::where('status', QuoteRequestStatus::Qualified)->count();

        $won = QuoteRequest::where('status', QuoteRequestStatus::Won)->count();
        $lost = QuoteRequest::where('status', QuoteRequestStatus::Lost)->count();

        $recentContacts = Contact::orderByDesc('submitted_at')->limit(5)->get()
            ->map(fn (Contact $contact) => [
                'type' => 'contact',
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'status' => $contact->status->value,
                'submitted_at' => $contact->submitted_at?->toIso8601String(),
            ]);

        $recentQuoteRequests = QuoteRequest::orderByDesc('submitted_at')->limit(5)->get()
            ->map(fn (QuoteRequest $quoteRequest) => [
                'type' => 'quote_request',
                'id' => $quoteRequest->id,
                'name' => $quoteRequest->name,
                'email' => $quoteRequest->email,
                'status' => $quoteRequest->status->value,
                'submitted_at' => $quoteRequest->submitted_at?->toIso8601String(),
            ]);

        $recentSubmissions = $recentContacts->concat($recentQuoteRequests)
            ->sortByDesc('submitted_at')
            ->take(10)
            ->values();

        return $this->success([
            'new_contacts' => $newContacts,
            'new_quote_requests' => $newQuoteRequests,
            'open_leads' => $openContacts + $openQuoteRequests,
            'qualified_leads' => $qualifiedContacts + $qualifiedQuoteRequests,
            'won_requests' => $won,
            'lost_requests' => $lost,
            'recent_submissions' => $recentSubmissions,
        ]);
    }
}
