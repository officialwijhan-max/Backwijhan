<?php

namespace App\Filament\Resources\QuoteRequests\Tables;

use App\Enums\QuoteRequestStatus;
use App\Filament\Actions\ChangeStatusAction;
use App\Http\Requests\Admin\UpdateQuoteRequestRequest;
use App\Models\QuoteRequest;
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

class QuoteRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable()->copyable(),
                TextColumn::make('company')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('project_type')
                    ->formatStateUsing(fn (?string $state): ?string => LeadLabels::projectType($state))
                    ->sortable(),
                TextColumn::make('budget_range')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(QuoteRequestStatus::class)
                    ->multiple(),
                // Submitted as free display text in EN or AR, so the options
                // come from what has actually been received.
                SelectFilter::make('project_type')
                    ->options(fn (): array => QuoteRequest::query()
                        ->distinct()
                        ->orderBy('project_type')
                        ->pluck('project_type', 'project_type')
                        ->all()),
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
                ChangeStatusAction::make(QuoteRequestStatus::class, UpdateQuoteRequestRequest::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
