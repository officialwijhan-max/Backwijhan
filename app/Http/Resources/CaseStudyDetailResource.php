<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyDetailResource extends JsonResource
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
            'headline' => $this->{"headline_{$locale}"} ?: $this->{"title_{$locale}"},
            'summary' => $this->{"summary_{$locale}"},
            'hero_description' => $this->{"hero_description_{$locale}"} ?: $this->{"summary_{$locale}"},
            'outcome' => $this->{"outcome_{$locale}"},
            'client_name' => $this->client_visibility ? $this->client_name : null,
            'featured' => (bool) $this->featured,
            'cover_image_url' => $this->cover_image_url,
            'logo' => $this->logo,
            'published_at' => $this->published_at?->toIso8601String(),
            'sections' => CaseStudySectionResource::collection($this->whenLoaded('sections')),
            'features' => CaseStudyFeatureResource::collection($this->whenLoaded('features')),
            'contributions' => CaseStudyContributionResource::collection($this->whenLoaded('contributions')),
            'technologies' => CaseStudyTechnologyResource::collection($this->whenLoaded('technologies')),
            'related' => CaseStudyResource::collection($this->whenLoaded('related')),
        ];
    }
}
