<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * A single code path serves both languages: the `locale` query parameter
     * (?locale=en|ar, defaulting to en) picks which translated columns to
     * expose, rather than duplicating this resource per language.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale') === 'ar' ? 'ar' : 'en';

        return [
            'id' => $this->id,
            'number' => $this->number,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'title' => $this->{"title_{$locale}"},
            'summary' => $this->{"summary_{$locale}"},
            'capabilities' => $this->{"capabilities_{$locale}"},
        ];
    }
}
