<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Support\EngagementOptions;
use Illuminate\Validation\Rule;

class ContactRequest extends ApiFormRequest
{
    /**
     * Stable, locale-independent keys the contact forms may post as
     * `project_type` besides an active service slug. The first four are the
     * "Product" options on the Contact page (src/routes/contact.tsx and
     * src/components/arabic-pages.tsx) — keep them in sync with those arrays.
     */
    public const PROJECT_TYPE_EXTRAS = [
        'new-product',
        'existing-product',
        'erp-operations',
        'embedded-team',
        'other',
        'not-sure',
    ];

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
            'project_type' => ['nullable', 'string', 'max:100', Rule::in($this->allowedProjectTypes())],
            'budget_range' => ['nullable', 'string', 'max:100', Rule::in(EngagementOptions::contactBudgetRanges())],
            'locale' => ['nullable', Rule::in(['en', 'ar'])],
        ];
    }

    /**
     * Read from the services table per request rather than hardcoded, so
     * renaming, adding or deactivating a service needs no code change here.
     *
     * @return list<string>
     */
    private function allowedProjectTypes(): array
    {
        $activeSlugs = Service::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->pluck('slug')
            ->all();

        return [...$activeSlugs, ...self::PROJECT_TYPE_EXTRAS];
    }
}
