<?php

namespace Database\Seeders;

use App\Models\WorkCategory;
use Illuminate\Database\Seeder;

/**
 * Mirrors the project categories from src/lib/site-data.ts (projectCategories)
 * and src/lib/site-data-ar.ts (projectCategoriesAr). No case studies are
 * seeded — the frontend's /work page itself states that verified project
 * details have not yet been supplied, so none are fabricated here either.
 */
class WorkCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['en' => 'ERP', 'ar' => 'أنظمة ERP'],
            ['en' => 'Healthcare', 'ar' => 'الرعاية الصحية'],
            ['en' => 'Agriculture', 'ar' => 'الزراعة'],
            ['en' => 'E-commerce', 'ar' => 'التجارة الإلكترونية'],
            ['en' => 'Mobile Applications', 'ar' => 'تطبيقات الهاتف'],
            ['en' => 'Automotive', 'ar' => 'السيارات'],
            ['en' => 'Social Platforms', 'ar' => 'المنصات الاجتماعية'],
            ['en' => 'Booking Platforms', 'ar' => 'منصات الحجز'],
            ['en' => 'Fintech', 'ar' => 'التقنية المالية'],
            ['en' => 'Sports Technology', 'ar' => 'التقنية الرياضية'],
        ];

        foreach ($categories as $index => $category) {
            WorkCategory::updateOrCreate(
                ['name_en' => $category['en']],
                ['name_ar' => $category['ar'], 'sort_order' => $index, 'is_active' => true],
            );
        }
    }
}
