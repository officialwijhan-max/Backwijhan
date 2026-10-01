<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContactStatus;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::enum(ContactStatus::class)],
        ];
    }
}
