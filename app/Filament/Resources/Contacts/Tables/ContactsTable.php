<?php

namespace App\Filament\Resources\Contacts\Tables;

use App\Enums\ContactStatus;
use App\Filament\Actions\ChangeStatusAction;
use App\Http\Requests\Admin\UpdateContactRequest;
use App\Http\Requests\ContactRequest;
use App\Support\LeadLabels;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable()->copyable(),
                TextColumn::make('company')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('subject')
                    ->formatStateUsing(fn (?string $state): ?string => LeadLabels::subject($state))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('locale')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): ?string => LeadLabels::locale($state))
                    ->color(fn (?string $state): string => $state === 'ar' ? 'success' : 'info')
                    ->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(ContactStatus::class)
                    ->multiple(),
                SelectFilter::make('subject')
                    ->options(LeadLabels::SUBJECTS),
                Filter::make('submitted_at')
                    ->schema([
                        DatePicker::make('from')->label('Submitted from'),
                        DatePicker::make('until')->label('Submitted until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('submitted_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('submitted_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
                ChangeStatusAction::make(ContactStatus::class, UpdateContactRequest::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
