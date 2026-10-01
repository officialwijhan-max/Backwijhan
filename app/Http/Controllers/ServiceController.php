<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ApiResponses;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Support\PublicContentCache;

class ServiceController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $services = PublicContentCache::remember('services.active', function () {
            return Service::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        return $this->success(ServiceResource::collection($services));
    }
}
