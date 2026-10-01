<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactClientConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact) {}

    public function envelope(): Envelope
    {
        $subject = $this->contact->locale === 'ar'
            ? 'وصلتنا رسالتك — وِجهان'
            : 'We received your message — Wijhan';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $view = $this->contact->locale === 'ar'
            ? 'emails.contact-confirmation-ar'
            : 'emails.contact-confirmation-en';

        return new Content(view: $view);
    }
}
