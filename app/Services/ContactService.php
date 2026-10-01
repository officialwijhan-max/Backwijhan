<?php

namespace App\Services;

use App\Enums\ContactStatus;
use App\Mail\ContactClientConfirmation;
use App\Mail\ContactTeamNotification;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactService
{
    public function submit(array $data, Request $request): Contact
    {
        $contact = Contact::create([
            ...$data,
            'locale' => $data['locale'] ?? 'en',
            'status' => ContactStatus::New,
            'source' => $data['source'] ?? 'website',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'submitted_at' => now(),
        ]);

        Log::info('Contact submitted', ['contact_id' => $contact->id, 'locale' => $contact->locale]);

        $this->notify($contact);

        return $contact;
    }

    private function notify(Contact $contact): void
    {
        try {
            Mail::to(config('mail.team_address'))->queue(new ContactTeamNotification($contact));
            Mail::to($contact->email)->queue(new ContactClientConfirmation($contact));
        } catch (Throwable $e) {
            // The submission is already persisted; a mail-provider outage
            // must not turn into a failed request for the client.
            Log::warning('Contact notification email failed to queue', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
