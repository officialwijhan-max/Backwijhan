<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudySectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale') === 'ar' ? 'ar' : 'en';

        return [
            'type' => $this->type,
            'title' => $this->{"title_{$locale}"},
            'content' => $this->{"content_{$locale}"},
        ];
    }
}
