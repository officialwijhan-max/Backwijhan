<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class CaseStudySection extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'case_study_id',
        'type',
        'title_en',
        'title_ar',
        'content_en',
        'content_ar',
        'display_order',
    ];

    public function caseStudy()
    {
        return $this->belongsTo(CaseStudy::class);
    }
}
