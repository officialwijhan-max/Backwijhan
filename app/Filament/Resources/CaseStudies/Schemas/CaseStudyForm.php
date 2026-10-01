<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CaseStudyForm
{
    /** Section types used by the published case studies; the column itself is free text. */
    private const SECTION_TYPES = ['challenge', 'product', 'talent', 'engagement', 'marketplace', 'content', 'engineering'];

    public static function configure(Schema $schema): Schema
    {
        $rtl = ['dir' => 'rtl'];

        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Case study')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                Section::make()->columns(2)->schema([
                                    Select::make('work_category_id')
                                        ->label('Category')
                                        ->relationship('workCategory', 'name_en')
                                        ->searchable()
                                        ->preload(),
                                    TextInput::make('slug')
                                        ->required()
                                        ->maxLength(255)
                                        ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                                        ->unique(ignoreRecord: true)
                                        ->helperText('Used in the public URL: /work/{slug}.'),
                                    TextInput::make('client_name')->maxLength(255),
                                    Toggle::make('client_visibility')
                                        ->label('Show client name publicly')
                                        ->default(true),
                                    TextInput::make('industry_en')->label('Industry (EN)')->maxLength(255),
                                    TextInput::make('industry_ar')->label('Industry (AR)')->maxLength(255)
                                        ->extraInputAttributes($rtl),
                                    TextInput::make('logo')
                                        ->maxLength(255)
                                        ->helperText('Path or URL of the logo, e.g. /case-studies/egyptian-coach/logo.png'),
                                    TextInput::make('cover_image_url')
                                        ->label('Cover image URL')
                                        ->maxLength(255),
                                    Toggle::make('is_published')
                                        ->label('Published')
                                        ->helperText('Only published case studies are returned by the public API.'),
                                    Toggle::make('featured'),
                                    TextInput::make('display_order')
                                        ->numeric()->integer()->minValue(0)->maxValue(65535)
                                        ->default(0)->required(),
                                    DateTimePicker::make('published_at')
                                        ->helperText('Set automatically on first publish if left empty.'),
                                ]),
                            ]),
                        Tab::make('English')
                            ->schema([
                                TextInput::make('title_en')->label('Title')->required()->maxLength(255),
                                TextInput::make('headline_en')->label('Headline')->maxLength(255),
                                Textarea::make('summary_en')->label('Summary')->required()->rows(3),
                                Textarea::make('hero_description_en')->label('Hero description')->rows(4),
                                TextInput::make('outcome_en')->label('Outcome')->maxLength(255),
                            ]),
                        Tab::make('Arabic')
                            ->schema([
                                TextInput::make('title_ar')->label('Title')->required()->maxLength(255)
                                    ->extraInputAttributes($rtl),
                                TextInput::make('headline_ar')->label('Headline')->maxLength(255)
                                    ->extraInputAttributes($rtl),
                                Textarea::make('summary_ar')->label('Summary')->required()->rows(3)
                                    ->extraInputAttributes($rtl),
                                Textarea::make('hero_description_ar')->label('Hero description')->rows(4)
                                    ->extraInputAttributes($rtl),
                                TextInput::make('outcome_ar')->label('Outcome')->maxLength(255)
                                    ->extraInputAttributes($rtl),
                            ]),
                        Tab::make('Sections')
                            ->schema([
                                Repeater::make('sections')
                                    ->relationship()
                                    ->orderColumn('display_order')
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => $state['title_en'] ?? $state['type'] ?? null)
                                    ->addActionLabel('Add section')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('type')
                                            ->required()->maxLength(255)
                                            ->datalist(self::SECTION_TYPES)
                                            ->helperText('e.g. challenge, product, engineering.')
                                            ->columnSpanFull(),
                                        TextInput::make('title_en')->label('Title (EN)')->maxLength(255),
                                        TextInput::make('title_ar')->label('Title (AR)')->maxLength(255)
                                            ->extraInputAttributes($rtl),
                                        Textarea::make('content_en')->label('Content (EN)')->rows(6),
                                        Textarea::make('content_ar')->label('Content (AR)')->rows(6)
                                            ->extraInputAttributes($rtl),
                                    ]),
                            ]),
                        Tab::make('Features')
                            ->schema([
                                Repeater::make('features')
                                    ->relationship()
                                    ->orderColumn('display_order')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['title_en'] ?? null)
                                    ->addActionLabel('Add feature')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('title_en')->label('Title (EN)')->required()->maxLength(255),
                                        TextInput::make('title_ar')->label('Title (AR)')->required()->maxLength(255)
                                            ->extraInputAttributes($rtl),
                                        TextInput::make('category_en')->label('Category (EN)')->maxLength(255),
                                        TextInput::make('category_ar')->label('Category (AR)')->maxLength(255)
                                            ->extraInputAttributes($rtl),
                                        Textarea::make('description_en')->label('Description (EN)')->rows(3),
                                        Textarea::make('description_ar')->label('Description (AR)')->rows(3)
                                            ->extraInputAttributes($rtl),
                                        TextInput::make('image')->maxLength(255)
                                            ->helperText('Path or URL of the feature image.'),
                                        TextInput::make('secondary_image')->maxLength(255),
                                    ]),
                            ]),
                        Tab::make('Contributions')
                            ->schema([
                                Repeater::make('contributions')
                                    ->relationship()
                                    ->orderColumn('display_order')
                                    ->itemLabel(fn (array $state): ?string => $state['name_en'] ?? null)
                                    ->addActionLabel('Add contribution')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name_en')->label('Name (EN)')->required()->maxLength(255),
                                        TextInput::make('name_ar')->label('Name (AR)')->required()->maxLength(255)
                                            ->extraInputAttributes($rtl),
                                    ]),
                            ]),
                        Tab::make('Technologies')
                            ->schema([
                                Repeater::make('technologies')
                                    ->relationship()
                                    ->orderColumn('display_order')
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add technology')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')->required()->maxLength(255),
                                        TextInput::make('category')->maxLength(255),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
