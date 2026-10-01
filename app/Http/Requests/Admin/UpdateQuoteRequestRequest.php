<?php

namespace App\Http\Requests\Admin;

use App\Enums\QuoteRequestStatus;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateQuoteRequestRequest extends ApiFormRequest
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
            'status' => ['sometimes', Rule::enum(QuoteRequestStatus::class)],
        ];
    }
}
