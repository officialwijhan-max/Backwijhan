<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Contacts\ContactResource;
use App\Models\Contact;
use App\Support\LeadLabels;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestContacts extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Latest contact requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Contact::query()->latest('submitted_at')->limit(5))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('subject')
                    ->formatStateUsing(fn (?string $state): ?string => LeadLabels::subject($state))
                    ->placeholder('—'),
                TextColumn::make('status')->badge(),
                TextColumn::make('submitted_at')->dateTime(),
            ])
            ->recordUrl(fn (Contact $record): string => ContactResource::getUrl('view', ['record' => $record]))
            ->paginated(false);
    }
}
