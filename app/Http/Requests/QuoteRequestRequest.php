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
            'project_type' => ['required', 'string', 'max:100', Rule::in($this->allowedProjectTypes())],
            // Optional: the Project Scoping forms label the budget "(optional)".
            'budget_range' => ['nullable', 'string', 'max:100', Rule::in(EngagementOptions::contactBudgetRanges())],
            'description' => ['required', 'string', 'max:5000'],
            'locale' => ['nullable', Rule::in(['en', 'ar'])],
        ];
    }

    /**
     * The Project Scoping forms post a service slug (or a stable extra such as
     * `not-sure`); older clients post the display text of a project type.
     *
     * @return list<string>
     */
    private function allowedProjectTypes(): array
    {
        return [...EngagementOptions::projectTypes(), ...EngagementOptions::serviceProjectTypes()];
    }
}
