<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Mirrors the six services the frontend's header mega-menu, footer, and
 * Services page all read from this same API. Idempotent by `slug`: existing
 * rows are updated in place, new ones created, and any row whose slug is not
 * in this list (including legacy rows seeded before `slug` existed) is
 * deactivated rather than deleted, so no data is destroyed by re-running it.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'slug' => 'custom-software',
                'number' => '01',
                'icon' => 'code',
                'title_en' => 'Custom Software',
                'title_ar' => 'الحلول والبرمجيات المخصصة',
                'summary_en' => 'Enterprise applications & platforms',
                'summary_ar' => 'تطبيقات ومنصات مؤسسية',
                'capabilities_en' => [],
                'capabilities_ar' => [],
            ],
            [
                'slug' => 'mobile-apps',
                'number' => '02',
                'icon' => 'smartphone',
                'title_en' => 'Mobile Applications',
                'title_ar' => 'تطبيقات الموبايل',
                'summary_en' => 'iOS & Android development',
                'summary_ar' => 'تطوير تطبيقات iOS وAndroid',
                'capabilities_en' => [],
                'capabilities_ar' => [],
            ],
            [
                'slug' => 'portals-websites',
                'number' => '03',
                'icon' => 'globe',
                'title_en' => 'Portals & Websites',
                'title_ar' => 'البوابات والمواقع',
                'summary_en' => 'Enterprise portals & web platforms',
                'summary_ar' => 'بوابات ومنصات ويب مؤسسية',
                'capabilities_en' => [],
                'capabilities_ar' => [],
            ],
            [
                'slug' => 'ui-ux',
                'number' => '04',
                'icon' => 'layout-grid',
                'title_en' => 'UI/UX Design',
                'title_ar' => 'تصميم واجهات وتجربة المستخدم',
                'summary_en' => 'Research-driven product design',
                'summary_ar' => 'تصميم منتجات مبني على البحث',
                'capabilities_en' => [],
                'capabilities_ar' => [],
            ],
            [
                'slug' => 'system-integration',
                'number' => '05',
                'icon' => 'workflow',
                'title_en' => 'System Integration',
                'title_ar' => 'تكامل الأنظمة',
                'summary_en' => 'APIs, middleware & connectors',
                'summary_ar' => 'واجهات برمجية وطبقات وسيطة وموصلات',
                'capabilities_en' => [],
                'capabilities_ar' => [],
            ],
            [
                'slug' => 'erp-solutions',
                'number' => '06',
                'icon' => 'database',
                'title_en' => 'ERP Solutions',
                'title_ar' => 'حلول ERP',
                'summary_en' => 'Tailored ERP systems for operations & finance',
                'summary_ar' => 'أنظمة ERP مصممة خصيصاً للعمليات والمالية',
                'capabilities_en' => [],
                'capabilities_ar' => [],
            ],
        ];

        $slugs = array_column($services, 'slug');

        foreach ($services as $index => $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                [...$service, 'sort_order' => $index, 'is_active' => true],
            );
        }

        $affected = Service::query()
            ->where(function ($query) use ($slugs) {
                $query->whereNotIn('slug', $slugs)->orWhereNull('slug');
            })
            ->where('is_active', true)
            ->get(['id', 'number', 'title_en']);

        if ($affected->isNotEmpty()) {
            $this->command?->warn('Deactivating services not in the new catalog:');
            foreach ($affected as $service) {
                $this->command?->line("  #{$service->id} [{$service->number}] {$service->title_en}");
            }
        }

        Service::query()
            ->where(function ($query) use ($slugs) {
                $query->whereNotIn('slug', $slugs)->orWhereNull('slug');
            })
            ->update(['is_active' => false]);

        Cache::forget('services.active');
    }
}
