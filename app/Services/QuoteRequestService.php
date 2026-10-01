<?php

namespace App\Services;

use App\Enums\QuoteRequestStatus;
use App\Mail\QuoteRequestClientConfirmation;
use App\Mail\QuoteRequestTeamNotification;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class QuoteRequestService
{
    public function submit(array $data, Request $request): QuoteRequest
    {
        $quoteRequest = QuoteRequest::create([
            ...$data,
            'locale' => $data['locale'] ?? 'en',
            'status' => QuoteRequestStatus::New,
            'source' => $data['source'] ?? 'website',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'submitted_at' => now(),
        ]);

        Log::info('Quote request submitted', ['quote_request_id' => $quoteRequest->id, 'locale' => $quoteRequest->locale]);

        $this->notify($quoteRequest);

        return $quoteRequest;
    }

    private function notify(QuoteRequest $quoteRequest): void
    {
        try {
            Mail::to(config('mail.team_address'))->queue(new QuoteRequestTeamNotification($quoteRequest));
            Mail::to($quoteRequest->email)->queue(new QuoteRequestClientConfirmation($quoteRequest));
        } catch (Throwable $e) {
            Log::warning('Quote request notification email failed to queue', [
                'quote_request_id' => $quoteRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
