<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteRequestClientConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public QuoteRequest $quoteRequest) {}

    public function envelope(): Envelope
    {
        $subject = $this->quoteRequest->locale === 'ar'
            ? 'وصلنا طلب عرض السعر الخاص بك — وِجهان'
            : 'We received your quote request — Wijhan';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $view = $this->quoteRequest->locale === 'ar'
            ? 'emails.quote-confirmation-ar'
            : 'emails.quote-confirmation-en';

        return new Content(view: $view);
    }
}
