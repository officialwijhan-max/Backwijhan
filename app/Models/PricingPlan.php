<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'number',
        'title_en',
        'title_ar',
        'tag_en',
        'tag_ar',
        'price_en',
        'price_ar',
        'price_note_en',
        'price_note_ar',
        'summary_en',
        'summary_ar',
        'includes_en',
        'includes_ar',
        'best_for_en',
        'best_for_ar',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'includes_en' => 'array',
            'includes_ar' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
