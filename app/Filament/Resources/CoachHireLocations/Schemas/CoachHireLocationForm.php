<?php

namespace App\Filament\Resources\CoachHireLocations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CoachHireLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Location')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->helperText('The city or town name, e.g. London.')
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the page URL, e.g. /coach-hire/london'),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Toggle::make('is_active')
                            ->label('Visible on the website')
                            ->default(true),
                        TextInput::make('intro_heading')
                            ->label('Page heading (H1)')
                            ->columnSpanFull()
                            ->helperText('The main headline shown at the top of the page, e.g. "Coach Hire in London for Groups of Every Size".'),
                        Textarea::make('intro_content')
                            ->label('Intro content')
                            ->rows(10)
                            ->columnSpanFull()
                            ->helperText('The introductory copy. Separate paragraphs with a blank line — each block becomes its own paragraph on the page.'),
                    ]),
                Section::make('Hero image')
                    ->description('A real, properly licensed photo of a recognisable local scene (skyline, landmark or street). Only use images you are licensed to use — your own photography, or Unsplash/Pexels-licensed. Do not reuse images from competitor sites.')
                    ->columns(2)
                    ->components([
                        FileUpload::make('hero_image')
                            ->label('Hero image')
                            ->helperText('Upload a WebP if you can, sized around 1600×700. Name the file descriptively before uploading, e.g. coach-hire-london-tower-bridge.webp — the filename is kept as-is for SEO.')
                            ->image()
                            ->disk('public')
                            ->directory('coach-hire-locations')
                            ->getUploadedFileNameForStorageUsing(
                                fn ($file) => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension()
                            )
                            ->imageEditor()
                            ->maxSize(4096),
                        TextInput::make('hero_image_alt')
                            ->label('Image alt text')
                            ->helperText('Describes the photo for screen readers and image search, e.g. "Coach hire in central London near Tower Bridge".'),
                        TextInput::make('hero_image_credit')
                            ->label('Image credit')
                            ->columnSpanFull()
                            ->helperText('Photo attribution shown subtly over the image. Required for licences such as CC BY / CC BY-SA. Basic HTML links are allowed, e.g. Photo © Jane Doe (CC BY-SA 3.0). Leave blank for your own photography.'),
                    ]),
                Section::make('Why choose us')
                    ->description('The bullet points that make this location unique. Aim for 3–5.')
                    ->components([
                        Repeater::make('why_choose_points')
                            ->hiddenLabel()
                            ->components([
                                TextInput::make('title')
                                    ->required(),
                                Textarea::make('description')
                                    ->rows(3),
                            ])
                            ->addActionLabel('Add a point')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->defaultItems(0),
                    ]),
                Section::make('FAQs')
                    ->description('Location-specific questions and answers. These also power the FAQ structured data for this page.')
                    ->components([
                        Repeater::make('faqs')
                            ->hiddenLabel()
                            ->components([
                                TextInput::make('question')
                                    ->required()
                                    ->columnSpanFull(),
                                Textarea::make('answer')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('Add a question')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                            ->defaultItems(0),
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
