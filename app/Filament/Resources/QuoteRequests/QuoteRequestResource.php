<?php

namespace App\Filament\Resources\QuoteRequests;

use App\Enums\QuoteRequestStatus;
use App\Filament\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Filament\Resources\QuoteRequests\Pages\ViewQuoteRequest;
use App\Filament\Resources\QuoteRequests\Schemas\QuoteRequestInfolist;
use App\Filament\Resources\QuoteRequests\Tables\QuoteRequestsTable;
use App\Models\QuoteRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|UnitEnum|null $navigationGroup = 'Leads';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'quote request';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'company'];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = QuoteRequest::where('status', QuoteRequestStatus::New)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New quote requests';
    }

    // Quote requests only arrive through the public API; admins triage
    // them (status) but never author or edit the submitter's content.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuoteRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuoteRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteRequests::route('/'),
            'view' => ViewQuoteRequest::route('/{record}'),
        ];
    }
}
