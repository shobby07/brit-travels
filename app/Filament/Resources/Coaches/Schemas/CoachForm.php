<?php

namespace App\Filament\Resources\Coaches\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CoachForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coach')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the page URL, e.g. /fleet/49-seater-coach'),
                        TextInput::make('seats')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Toggle::make('is_active')
                            ->label('Visible on the website')
                            ->default(true),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                        TagsInput::make('amenities')
                            ->placeholder('Type an amenity and press Enter')
                            ->columnSpanFull(),
                    ]),
                Section::make('Images')
                    ->components([
                        FileUpload::make('image')
                            ->label('Main image')
                            ->image()
                            ->disk('public')
                            ->directory('coaches')
                            ->imageEditor()
                            ->maxSize(4096),
                        FileUpload::make('gallery')
                            ->label('Gallery images')
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('coaches/gallery')
                            ->reorderable()
                            ->maxSize(4096),
                    ]),
                Section::make('SEO')
                    ->components([
                        TextInput::make('meta_title')
                            ->helperText('Shown as the Google result title. Leave blank to auto-generate.'),
                        Textarea::make('meta_description')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Shown as the Google result snippet. Aim for 120–160 characters.'),
                    ])
                    ->collapsible(),
            ]);
    }
}
