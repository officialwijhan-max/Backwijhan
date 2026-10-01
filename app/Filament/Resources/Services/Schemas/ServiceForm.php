<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->columns(2)
                    ->schema([
                        TextInput::make('number')
                            ->required()
                            ->maxLength(4)
                            ->helperText('Display number shown on the services page, e.g. 01.'),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->unique(ignoreRecord: true)
                            ->helperText('Lowercase letters, numbers and dashes. Also accepted as the "project_type" value on the public contact form while the service is active, so changing it affects that form.'),
                        TextInput::make('icon')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Icon name used by the website, e.g. code, smartphone, globe.'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(65535)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active (visible on the website)')
                            ->default(true),
                    ]),
                Section::make('English')
                    ->schema([
                        TextInput::make('title_en')->label('Title')->required()->maxLength(255),
                        Textarea::make('summary_en')->label('Summary')->required()->maxLength(255)->rows(2),
                        Repeater::make('capabilities_en')
                            ->label('Capabilities')
                            ->simple(TextInput::make('capability')->required()->maxLength(255))
                            ->required()
                            ->reorderable()
                            ->addActionLabel('Add capability'),
                    ]),
                Section::make('Arabic')
                    ->schema([
                        TextInput::make('title_ar')->label('Title')->required()->maxLength(255)
                            ->extraInputAttributes(['dir' => 'rtl']),
                        Textarea::make('summary_ar')->label('Summary')->required()->maxLength(255)->rows(2)
                            ->extraInputAttributes(['dir' => 'rtl']),
                        Repeater::make('capabilities_ar')
                            ->label('Capabilities')
                            ->simple(TextInput::make('capability')->required()->maxLength(255)
                                ->extraInputAttributes(['dir' => 'rtl']))
                            ->required()
                            ->reorderable()
                            ->addActionLabel('Add capability'),
                    ]),
            ]);
    }
}
