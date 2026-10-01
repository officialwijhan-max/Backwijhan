<?php

namespace App\Http\Controllers\Admin;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Contact::class);

        $contacts = Contact::query()
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('locale'), fn ($query, $locale) => $query->where('locale', $locale))
            ->orderByDesc('submitted_at')
            ->paginate(max(1, min((int) $request->query('per_page', 15), 100)));

        return $this->success([
            'items' => ContactResource::collection($contacts->items()),
            'pagination' => [
                'current_page' => $contacts->currentPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
                'last_page' => $contacts->lastPage(),
            ],
        ]);
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        return $this->success(new ContactResource($contact));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $contact->update($request->validated());

        Log::info('Admin updated contact', ['contact_id' => $contact->id, 'admin_id' => $request->user()->id]);

        return $this->success(new ContactResource($contact), 'Contact updated.');
    }

    public function destroy(Request $request, Contact $contact)
    {
        $this->authorize('delete', $contact);

        Log::info('Admin deleted contact', ['contact_id' => $contact->id, 'admin_id' => $request->user()->id]);

        $contact->delete();

        return $this->success(null, 'Contact deleted.');
    }
}
