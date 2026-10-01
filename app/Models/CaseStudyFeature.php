<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class CaseStudyFeature extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'case_study_id',
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'image',
        'secondary_image',
        'category_en',
        'category_ar',
        'display_order',
    ];

    public function caseStudy()
    {
        return $this->belongsTo(CaseStudy::class);
    }
}
