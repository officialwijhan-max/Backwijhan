<?php

namespace App\Support;

use App\Models\Service;

/**
 * Human-readable names for the stable keys the public forms submit
 * (`subject`, `project_type`), used by the admin panel only.
 *
 * Anything that is not a known key — including older rows that stored the
 * display text itself, in English or Arabic — is shown exactly as stored.
 */
class LeadLabels
{
    /** Keys of ContactRequest::SUBJECTS. */
    public const SUBJECTS = [
        'scoping' => 'Project Scoping',
        'call' => 'Schedule a Call',
        'partnership' => 'Partnership',
        'support' => 'Support',
    ];

    /** Keys of ContactRequest::PROJECT_TYPE_EXTRAS. */
    public const PROJECT_TYPE_EXTRAS = [
        'new-product' => 'New product',
        'existing-product' => 'Existing product improvement',
        'erp-operations' => 'ERP & operations',
        'embedded-team' => 'Embedded team',
        'other' => 'Other',
        'not-sure' => 'Not sure yet',
    ];

    public const LOCALES = [
        'en' => 'EN',
        'ar' => 'AR',
    ];

    /** @var array<string, string>|null service slug => English title, loaded once per request */
    private static ?array $serviceTitles = null;

    public static function subject(?string $value): ?string
    {
        return $value === null ? null : (self::SUBJECTS[$value] ?? $value);
    }

    public static function projectType(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::PROJECT_TYPE_EXTRAS[$value] ?? self::serviceTitles()[$value] ?? $value;
    }

    public static function locale(?string $value): ?string
    {
        return $value === null ? null : (self::LOCALES[$value] ?? strtoupper($value));
    }

    /** Forget the per-request service title map (used by tests). */
    public static function flush(): void
    {
        self::$serviceTitles = null;
    }

    /**
     * Includes inactive services: a lead keeps its slug after the service is
     * switched off, and should still read as a name.
     *
     * @return array<string, string>
     */
    private static function serviceTitles(): array
    {
        return self::$serviceTitles ??= Service::query()
            ->whereNotNull('slug')
            ->pluck('title_en', 'slug')
            ->all();
    }
}
