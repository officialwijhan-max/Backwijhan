<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingPlanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale') === 'ar' ? 'ar' : 'en';

        return [
            'id' => $this->id,
            'number' => $this->number,
            'title' => $this->{"title_{$locale}"},
            'tag' => $this->{"tag_{$locale}"},
            'price' => $this->{"price_{$locale}"},
            'price_note' => $this->{"price_note_{$locale}"},
            'summary' => $this->{"summary_{$locale}"},
            'includes' => $this->{"includes_{$locale}"},
            'best_for' => $this->{"best_for_{$locale}"},
        ];
    }
}
