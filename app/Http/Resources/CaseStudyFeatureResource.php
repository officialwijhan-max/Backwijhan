<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyFeatureResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale') === 'ar' ? 'ar' : 'en';

        return [
            'title' => $this->{"title_{$locale}"},
            'description' => $this->{"description_{$locale}"},
            'image' => $this->image,
            'secondary_image' => $this->secondary_image,
            'category' => $this->{"category_{$locale}"},
        ];
    }
}
