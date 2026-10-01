<?php

namespace App\Filament\Resources\CaseStudies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CaseStudiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_en')->label('Title')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('workCategory.name_en')->label('Category')->placeholder('—')->sortable(),
                TextColumn::make('client_name')->searchable()->placeholder('—'),
                IconColumn::make('is_published')->label('Published')->boolean()->sortable(),
                IconColumn::make('featured')->boolean()->sortable(),
                TextColumn::make('display_order')->label('Order')->sortable(),
                TextColumn::make('published_at')->dateTime()->sortable()->placeholder('—'),
            ])
            ->defaultSort('display_order')
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
                TernaryFilter::make('featured'),
                SelectFilter::make('work_category_id')
                    ->label('Category')
                    ->relationship('workCategory', 'name_en'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
