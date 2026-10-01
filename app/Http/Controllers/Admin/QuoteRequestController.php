<?php

namespace App\Http\Controllers\Admin;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateQuoteRequestRequest;
use App\Http\Resources\QuoteRequestResource;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuoteRequestController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $this->authorize('viewAny', QuoteRequest::class);

        $quoteRequests = QuoteRequest::query()
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('locale'), fn ($query, $locale) => $query->where('locale', $locale))
            ->orderByDesc('submitted_at')
            ->paginate(max(1, min((int) $request->query('per_page', 15), 100)));

        return $this->success([
            'items' => QuoteRequestResource::collection($quoteRequests->items()),
            'pagination' => [
                'current_page' => $quoteRequests->currentPage(),
                'per_page' => $quoteRequests->perPage(),
                'total' => $quoteRequests->total(),
                'last_page' => $quoteRequests->lastPage(),
            ],
        ]);
    }

    public function show(QuoteRequest $quoteRequest)
    {
        $this->authorize('view', $quoteRequest);

        return $this->success(new QuoteRequestResource($quoteRequest));
    }

    public function update(UpdateQuoteRequestRequest $request, QuoteRequest $quoteRequest)
    {
        $this->authorize('update', $quoteRequest);

        $quoteRequest->update($request->validated());

        Log::info('Admin updated quote request', ['quote_request_id' => $quoteRequest->id, 'admin_id' => $request->user()->id]);

        return $this->success(new QuoteRequestResource($quoteRequest), 'Quote request updated.');
    }

    public function destroy(Request $request, QuoteRequest $quoteRequest)
    {
        $this->authorize('delete', $quoteRequest);

        Log::info('Admin deleted quote request', ['quote_request_id' => $quoteRequest->id, 'admin_id' => $request->user()->id]);

        $quoteRequest->delete();

        return $this->success(null, 'Quote request deleted.');
    }
}
