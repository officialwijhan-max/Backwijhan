<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class CaseStudyContribution extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'case_study_id',
        'name_en',
        'name_ar',
        'display_order',
    ];

    public function caseStudy()
    {
        return $this->belongsTo(CaseStudy::class);
    }
}
