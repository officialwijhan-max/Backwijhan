<?php

namespace App\Support;

use App\Models\Service;

/**
 * The Project Type / Budget Range option values as they are literally submitted
 * by the existing frontend's <select> elements (src/routes/contact.tsx,
 * src/routes/pricing.tsx and their Arabic equivalents in
 * src/components/arabic-pages.tsx). The frontend posts the option's display
 * text as its value, in whichever language the page was rendered in, so
 * validation must accept both sets without duplicating business logic per
 * locale — the submitted string is stored as-is and `locale` records which
 * language it came from.
 */
class EngagementOptions
{
    /**
     * Stable, locale-independent keys the forms may post as `project_type`
     * besides an active service slug. The first four are the "Product"
     * options on the Contact page (src/routes/contact.tsx and
     * src/components/arabic-pages.tsx) — keep them in sync with those arrays.
     */
    public const PROJECT_TYPE_EXTRAS = [
        'new-product',
        'existing-product',
        'erp-operations',
        'embedded-team',
        'other',
        'not-sure',
    ];

    public const PROJECT_TYPES_EN = [
        'New digital product',
        'Existing product improvement',
        'ERP solution',
        'Product discovery',
        'Design',
        'Engineering',
        'Other',
    ];

    public const PROJECT_TYPES_AR = [
        'منتج رقمي جديد',
        'تحسين منتج قائم',
        'حل ERP',
        'اكتشاف المنتج',
        'التصميم',
        'الهندسة',
        'أخرى',
    ];

    public const BUDGET_RANGES_EN = [
        'Not decided yet',
        'Under $10,000',
        '$10,000–$25,000',
        '$25,000–$50,000',
        '$50,000+',
    ];

    public const BUDGET_RANGES_AR = [
        'لم نحددها بعد',
        'أقل من 10,000 دولار',
        '10,000–25,000 دولار',
        '25,000–50,000 دولار',
        'أكثر من 50,000 دولار',
    ];

    /**
     * Budget labels posted by the Contact page's "Get started" form
     * (src/components/get-started-section.tsx), which uses EGP bands rather
     * than the USD bands of the quote form.
     */
    public const CONTACT_BUDGET_RANGES = [
        'Under EGP 100,000',
        'EGP 100,000 – 250,000',
        'EGP 250,000 – 1,000,000',
        'EGP 1,000,000+',
        'Not sure — I need advice',
        'أقل من 100,000 جنيه',
        '100,000 – 250,000 جنيه',
        '250,000 – 1,000,000 جنيه',
        'أكثر من 1,000,000 جنيه',
        'غير متأكد — أحتاج استشارة',
    ];

    public static function contactBudgetRanges(): array
    {
        return [...self::budgetRanges(), ...self::CONTACT_BUDGET_RANGES];
    }

    /**
     * Active service slugs plus the stable extras. Read from the services
     * table per request rather than hardcoded, so renaming, adding or
     * deactivating a service needs no code change in the form requests.
     *
     * @return list<string>
     */
    public static function serviceProjectTypes(): array
    {
        $activeSlugs = Service::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->pluck('slug')
            ->all();

        return [...$activeSlugs, ...self::PROJECT_TYPE_EXTRAS];
    }

    public static function projectTypes(): array
    {
        return [...self::PROJECT_TYPES_EN, ...self::PROJECT_TYPES_AR];
    }

    public static function budgetRanges(): array
    {
        return [...self::BUDGET_RANGES_EN, ...self::BUDGET_RANGES_AR];
    }
}
