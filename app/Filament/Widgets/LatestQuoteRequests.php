<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Models\QuoteRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestQuoteRequests extends TableWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Latest quote requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => QuoteRequest::query()->latest('submitted_at')->limit(5))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('project_type'),
                TextColumn::make('status')->badge(),
                TextColumn::make('submitted_at')->dateTime(),
            ])
            ->recordUrl(fn (QuoteRequest $record): string => QuoteRequestResource::getUrl('view', ['record' => $record]))
            ->paginated(false);
    }
}
