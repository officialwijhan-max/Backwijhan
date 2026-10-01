<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ApiResponses;
use App\Http\Requests\ContactRequest;
use App\Services\ContactService;

class ContactController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly ContactService $contacts) {}

    public function store(ContactRequest $request)
    {
        $contact = $this->contacts->submit($request->validated(), $request);

        return $this->success(
            ['id' => $contact->id, 'status' => $contact->status->value],
            __('Your request has been received.'),
            201,
        );
    }
}
