<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale') === 'ar' ? 'ar' : 'en';

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->whenLoaded('workCategory', fn () => $this->workCategory?->{"name_{$locale}"}),
            'industry' => $this->{"industry_{$locale}"},
            'title' => $this->{"title_{$locale}"},
            'summary' => $this->{"summary_{$locale}"},
            'outcome' => $this->{"outcome_{$locale}"},
            'cover_image_url' => $this->cover_image_url,
            'featured' => (bool) $this->featured,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
