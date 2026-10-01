<?php

namespace App\Filament\Support;

use App\Support\LeadLabels;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

/**
 * Infolist entries shared by the Contact and Quote Request view pages, so
 * both leads read the same way in the admin panel.
 */
class LeadEntries
{
    /**
     * The visitor's free text: full width, line breaks preserved, and aligned
     * to the start of its own script (right for Arabic, left for English).
     * The styled wrapper is written in one piece around the escaped text so
     * no template whitespace is ever rendered as a leading indent.
     */
    public static function message(string $name): TextEntry
    {
        return TextEntry::make($name)
            ->columnSpanFull()
            ->html()
            ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString(
                '<div dir="auto" class="lead-message" style="white-space: pre-wrap; text-align: start; overflow-wrap: anywhere;">'.e((string) $state).'</div>'
            ));
    }

    public static function subject(): TextEntry
    {
        return TextEntry::make('subject')
            ->formatStateUsing(fn (?string $state): ?string => LeadLabels::subject($state))
            ->placeholder('—');
    }

    public static function projectType(bool $required = false): TextEntry
    {
        $entry = TextEntry::make('project_type')
            ->formatStateUsing(fn (?string $state): ?string => LeadLabels::projectType($state));

        return $required ? $entry : $entry->placeholder('—');
    }

    public static function locale(): TextEntry
    {
        return TextEntry::make('locale')
            ->badge()
            ->formatStateUsing(fn (?string $state): ?string => LeadLabels::locale($state))
            ->color(fn (?string $state): string => $state === 'ar' ? 'success' : 'info');
    }
}
