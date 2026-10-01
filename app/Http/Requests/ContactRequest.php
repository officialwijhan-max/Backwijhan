<?php

namespace App\Http\Requests;

use App\Support\EngagementOptions;
use Illuminate\Validation\Rule;

class ContactRequest extends ApiFormRequest
{
    /** @see EngagementOptions::PROJECT_TYPE_EXTRAS */
    public const PROJECT_TYPE_EXTRAS = EngagementOptions::PROJECT_TYPE_EXTRAS;

    /** Inquiry-type tabs on the service detail page (src/components/service-detail-page.tsx). */
    public const SUBJECTS = ['scoping', 'call', 'partnership', 'support'];

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
            'subject' => ['nullable', 'string', 'max:150', Rule::in(self::SUBJECTS)],
            'message' => ['required', 'string', 'max:5000'],
            'project_type' => ['nullable', 'string', 'max:100', Rule::in(EngagementOptions::serviceProjectTypes())],
            'budget_range' => ['nullable', 'string', 'max:100', Rule::in(EngagementOptions::contactBudgetRanges())],
            'locale' => ['nullable', Rule::in(['en', 'ar'])],
        ];
    }
}
