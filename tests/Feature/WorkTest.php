<?php

namespace Tests\Feature;

use App\Models\CaseStudy;
use App\Models\CaseStudyContribution;
use App\Models\CaseStudyFeature;
use App\Models\CaseStudySection;
use App\Models\CaseStudyTechnology;
use App\Models\WorkCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkTest extends TestCase
{
    use RefreshDatabase;

    private function createCaseStudy(array $overrides = []): CaseStudy
    {
        $categoryId = $overrides['work_category_id']
            ?? WorkCategory::firstOrCreate(
                ['name_en' => 'ERP'],
                ['name_ar' => 'أنظمة ERP', 'sort_order' => 0, 'is_active' => true],
            )->id;

        return CaseStudy::create(array_merge([
            'work_category_id' => $categoryId,
            'slug' => 'sample-project',
            'industry_en' => 'Logistics',
            'industry_ar' => 'الخدمات اللوجستية',
            'title_en' => 'Sample Project',
            'title_ar' => 'مشروع تجريبي',
            'client_name' => 'Sample Client',
            'client_visibility' => true,
            'headline_en' => 'A resilient logistics platform',
            'headline_ar' => 'منصة لوجستية مرنة',
            'summary_en' => 'Short summary.',
            'summary_ar' => 'ملخص قصير.',
            'hero_description_en' => 'Hero description.',
            'hero_description_ar' => 'وصف الصفحة الرئيسية.',
            'outcome_en' => 'Shipped on schedule.',
            'outcome_ar' => 'تم التسليم في الموعد.',
            'cover_image_url' => 'https://example.com/cover.jpg',
            'logo' => 'https://example.com/logo.svg',
            'is_published' => true,
            'featured' => false,
            'display_order' => 0,
            'published_at' => now(),
        ], $overrides));
    }

    public function test_index_returns_only_published_case_studies_and_active_categories(): void
    {
        $this->createCaseStudy();
        $this->createCaseStudy(['slug' => 'unpublished-project', 'is_published' => false]);
        WorkCategory::create(['name_en' => 'Inactive', 'name_ar' => 'غير نشط', 'sort_order' => 1, 'is_active' => false]);

        $response = $this->getJson('/api/v1/work');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.case_studies')
            ->assertJsonCount(1, 'data.categories')
            ->assertJsonPath('data.case_studies.0.slug', 'sample-project');
    }

    public function test_show_returns_full_case_study_detail_with_nested_data(): void
    {
        $caseStudy = $this->createCaseStudy();

        CaseStudySection::create([
            'case_study_id' => $caseStudy->id,
            'type' => 'challenge',
            'title_en' => 'The Challenge',
            'title_ar' => 'التحدي',
            'content_en' => 'Fragmented dispatch data.',
            'content_ar' => 'بيانات إرسال مجزأة.',
            'display_order' => 0,
        ]);

        CaseStudyFeature::create([
            'case_study_id' => $caseStudy->id,
            'title_en' => 'Live tracking',
            'title_ar' => 'تتبع مباشر',
            'description_en' => 'Real-time shipment visibility.',
            'description_ar' => 'رؤية مباشرة للشحنات.',
            'display_order' => 0,
        ]);

        CaseStudyContribution::create([
            'case_study_id' => $caseStudy->id,
            'name_en' => 'Backend Development',
            'name_ar' => 'تطوير الخلفية',
            'display_order' => 0,
        ]);

        CaseStudyTechnology::create([
            'case_study_id' => $caseStudy->id,
            'name' => 'Laravel',
            'category' => 'backend',
            'display_order' => 0,
        ]);

        $response = $this->getJson('/api/v1/work/sample-project');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'sample-project')
            ->assertJsonPath('data.title', 'Sample Project')
            ->assertJsonPath('data.client_name', 'Sample Client')
            ->assertJsonPath('data.sections.0.type', 'challenge')
            ->assertJsonPath('data.features.0.title', 'Live tracking')
            ->assertJsonPath('data.contributions.0.name', 'Backend Development')
            ->assertJsonPath('data.technologies.0.name', 'Laravel');
    }

    public function test_show_respects_arabic_locale(): void
    {
        $this->createCaseStudy();

        $response = $this->getJson('/api/v1/work/sample-project?locale=ar');

        $response->assertStatus(200)->assertJsonPath('data.title', 'مشروع تجريبي');
    }

    public function test_show_hides_client_name_when_visibility_is_disabled(): void
    {
        $this->createCaseStudy(['client_visibility' => false]);

        $response = $this->getJson('/api/v1/work/sample-project');

        $response->assertStatus(200)->assertJsonPath('data.client_name', null);
    }

    public function test_show_returns_404_for_unpublished_case_study(): void
    {
        $this->createCaseStudy(['slug' => 'draft-project', 'is_published' => false]);

        $this->getJson('/api/v1/work/draft-project')->assertStatus(404);
    }

    public function test_show_returns_404_for_missing_slug(): void
    {
        $this->getJson('/api/v1/work/does-not-exist')->assertStatus(404);
    }

    public function test_show_returns_related_published_case_studies_from_same_category(): void
    {
        $category = WorkCategory::create([
            'name_en' => 'ERP',
            'name_ar' => 'أنظمة ERP',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $primary = $this->createCaseStudy(['work_category_id' => $category->id]);
        $this->createCaseStudy(['work_category_id' => $category->id, 'slug' => 'related-project']);
        $this->createCaseStudy(['work_category_id' => null, 'slug' => 'unrelated-project']);

        $response = $this->getJson("/api/v1/work/{$primary->slug}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.related')
            ->assertJsonPath('data.related.0.slug', 'related-project');
    }
}
