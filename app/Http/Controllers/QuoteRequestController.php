<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ApiResponses;
use App\Http\Requests\QuoteRequestRequest;
use App\Services\QuoteRequestService;

class QuoteRequestController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly QuoteRequestService $quoteRequests) {}

    public function store(QuoteRequestRequest $request)
    {
        $quoteRequest = $this->quoteRequests->submit($request->validated(), $request);

        return $this->success(
            ['id' => $quoteRequest->id, 'status' => $quoteRequest->status->value],
            __('Your request has been received.'),
            201,
        );
    }
}
