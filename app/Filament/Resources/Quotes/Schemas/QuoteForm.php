<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Quote;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quotation')
                    ->columns(2)
                    ->components([
                        TextInput::make('reference')
                            ->default(fn () => Quote::generateReference())
                            ->required()
                            ->readOnlyOn('edit'),
                        Select::make('status')
                            ->options(Quote::STATUSES)
                            ->default(Quote::STATUS_NEW)
                            ->required()
                            ->native(false),
                    ]),
                Section::make('Trip details')
                    ->columns(2)
                    ->components([
                        Select::make('trip_type')
                            ->label('Trip type')
                            ->options(['one_way' => 'One way', 'round_trip' => 'Round trip'])
                            ->native(false)
                            ->live(),
                        Select::make('coach_id')
                            ->label('Coach')
                            ->relationship('coach', 'name')
                            ->placeholder('Not specified')
                            ->native(false),
                        TextInput::make('pickup_location')
                            ->required(),
                        TextInput::make('dropoff_location')
                            ->label('Drop-off location')
                            ->required(),
                        DatePicker::make('pickup_date'),
                        TimePicker::make('pickup_time')
                            ->seconds(false),
                        DatePicker::make('return_date')
                            ->visible(fn ($get) => $get('trip_type') === 'round_trip'),
                        TimePicker::make('return_time')
                            ->seconds(false)
                            ->visible(fn ($get) => $get('trip_type') === 'round_trip'),
                        TextInput::make('passengers')
                            ->numeric()
                            ->minValue(1),
                    ]),
                Section::make('Customer')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->tel()
                            ->required(),
                        Textarea::make('message')
                            ->label('Customer message')
                            ->columnSpanFull(),
                    ]),
                Section::make('Internal')
                    ->components([
                        Textarea::make('admin_notes')
                            ->label('Admin notes (never shown to the customer)')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
