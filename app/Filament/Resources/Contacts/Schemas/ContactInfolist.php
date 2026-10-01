<?php

namespace App\Filament\Resources\Contacts\Schemas;

use App\Filament\Support\LeadEntries;
use App\Models\Contact;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInfolist
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
                            ->url(fn (Contact $record): string => 'mailto:'.$record->email),
                        TextEntry::make('phone')->placeholder('—')->copyable(),
                    ]),
                Section::make('Enquiry')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')->badge(),
                        LeadEntries::subject(),
                        LeadEntries::projectType(),
                        TextEntry::make('budget_range')->placeholder('—'),
                        LeadEntries::message('message'),
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
