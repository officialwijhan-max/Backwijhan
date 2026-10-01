<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class WorkCategory extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'name_en',
        'name_ar',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function caseStudies()
    {
        return $this->hasMany(CaseStudy::class);
    }
}
