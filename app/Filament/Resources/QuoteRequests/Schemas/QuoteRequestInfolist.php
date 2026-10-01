<?php

namespace App\Filament\Resources\QuoteRequests\Schemas;

use App\Filament\Support\LeadEntries;
use App\Models\QuoteRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sender')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('company')->placeholder('—'),
                        TextEntry::make('email')
                            ->copyable()
                            ->url(fn (QuoteRequest $record): string => 'mailto:'.$record->email),
                        TextEntry::make('phone')->placeholder('—')->copyable(),
                    ]),
                Section::make('Project')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')->badge(),
                        LeadEntries::projectType(required: true),
                        TextEntry::make('budget_range')->placeholder('—'),
                        LeadEntries::message('description'),
                    ]),
                Section::make('Submission details')
                    ->columnSpanFull()
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('submitted_at')->dateTime(),
                        LeadEntries::locale(),
                        TextEntry::make('source'),
                        TextEntry::make('ip_address')->placeholder('—'),
                        TextEntry::make('user_agent')->placeholder('—')->columnSpan(2),
                    ]),
            ]);
    }
}
