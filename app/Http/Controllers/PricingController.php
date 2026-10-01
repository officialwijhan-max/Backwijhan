<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ApiResponses;
use App\Http\Resources\PricingPlanResource;
use App\Models\PricingPlan;
use App\Support\PublicContentCache;

class PricingController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $plans = PublicContentCache::remember('pricing_plans.active', function () {
            return PricingPlan::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        return $this->success(PricingPlanResource::collection($plans));
    }
}
