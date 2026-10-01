<?php

namespace App\Http\Requests;

use App\Support\EngagementOptions;
use Illuminate\Validation\Rule;

class QuoteRequestRequest extends ApiFormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'project_type' => ['required', 'string', Rule::in(EngagementOptions::projectTypes())],
            'budget_range' => ['required', 'string', Rule::in(EngagementOptions::budgetRanges())],
            'description' => ['required', 'string', 'max:5000'],
            'locale' => ['nullable', Rule::in(['en', 'ar'])],
        ];
    }
}
