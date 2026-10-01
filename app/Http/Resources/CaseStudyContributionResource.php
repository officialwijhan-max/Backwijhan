<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyContributionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale') === 'ar' ? 'ar' : 'en';

        return [
            'name' => $this->{"name_{$locale}"},
        ];
    }
}
