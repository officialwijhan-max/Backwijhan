<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ApiResponses;
use App\Http\Resources\CaseStudyDetailResource;
use App\Http\Resources\CaseStudyResource;
use App\Http\Resources\WorkCategoryResource;
use App\Models\CaseStudy;
use App\Models\WorkCategory;
use App\Support\PublicContentCache;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        // Cached as raw models (locale is applied by the resources below), and
        // flushed by FlushesPublicContentCache whenever an admin edits content.
        [$categories, $caseStudies] = PublicContentCache::remember('work.index', function () {
            $categories = WorkCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            // No verified case studies have been supplied yet (matches the
            // frontend's own "Selected work is being prepared" placeholder on
            // /work) — this returns whatever has actually been published rather
            // than fabricating example projects.
            $caseStudies = CaseStudy::query()
                ->where('is_published', true)
                ->with('workCategory')
                ->orderByDesc('featured')
                ->orderBy('display_order')
                ->orderByDesc('published_at')
                ->get();

            return [$categories, $caseStudies];
        });

        return $this->success([
            'categories' => WorkCategoryResource::collection($categories),
            'case_studies' => CaseStudyResource::collection($caseStudies),
        ]);
    }

    public function show(Request $request, string $slug)
    {
        // An unknown slug throws inside the callback, so a 404 is never cached.
        $caseStudy = PublicContentCache::remember('work.show.'.sha1($slug), function () use ($slug) {
            $caseStudy = CaseStudy::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->with([
                    'workCategory',
                    'sections',
                    'features',
                    'contributions',
                    'technologies',
                ])
                ->firstOrFail();

            // A case study with no category has nothing verified to match
            // related work against — show none rather than falling back to
            // "any other published case study" for it.
            $related = $caseStudy->work_category_id
                ? CaseStudy::query()
                    ->where('is_published', true)
                    ->where('id', '!=', $caseStudy->id)
                    ->where('work_category_id', $caseStudy->work_category_id)
                    ->orderByDesc('published_at')
                    ->limit(3)
                    ->get()
                : collect();

            $caseStudy->setRelation('related', $related);

            return $caseStudy;
        });

        return $this->success(new CaseStudyDetailResource($caseStudy));
    }
}
