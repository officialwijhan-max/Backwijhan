<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'number',
        'slug',
        'icon',
        'title_en',
        'title_ar',
        'summary_en',
        'summary_ar',
        'capabilities_en',
        'capabilities_ar',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capabilities_en' => 'array',
            'capabilities_ar' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
