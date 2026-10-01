<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'work_category_id',
        'slug',
        'industry_en',
        'industry_ar',
        'title_en',
        'title_ar',
        'client_name',
        'client_visibility',
        'headline_en',
        'headline_ar',
        'summary_en',
        'summary_ar',
        'hero_description_en',
        'hero_description_ar',
        'outcome_en',
        'outcome_ar',
        'cover_image_url',
        'logo',
        'is_published',
        'featured',
        'display_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'client_visibility' => 'boolean',
            'is_published' => 'boolean',
            'featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function workCategory()
    {
        return $this->belongsTo(WorkCategory::class);
    }

    public function sections()
    {
        return $this->hasMany(CaseStudySection::class)->orderBy('display_order');
    }

    public function features()
    {
        return $this->hasMany(CaseStudyFeature::class)->orderBy('display_order');
    }

    public function contributions()
    {
        return $this->hasMany(CaseStudyContribution::class)->orderBy('display_order');
    }

    public function technologies()
    {
        return $this->hasMany(CaseStudyTechnology::class)->orderBy('display_order');
    }
}
