<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Mail\BookingStatusMail;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Booking::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        Booking::STATUS_PENDING => 'warning',
                        Booking::STATUS_CONFIRMED => 'success',
                        Booking::STATUS_CANCELLED => 'danger',
                        Booking::STATUS_COMPLETED => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('name')
                    ->label('Customer')
                    ->searchable()
                    ->description(fn (Booking $record) => $record->phone),
                TextColumn::make('trip_type')
                    ->label('Trip')
                    ->formatStateUsing(fn (string $state) => $state === 'round_trip' ? 'Round trip' : 'One way'),
                TextColumn::make('pickup_location')
                    ->label('Route')
                    ->searchable()
                    ->formatStateUsing(fn (Booking $record) => $record->pickup_location.' → '.$record->dropoff_location)
                    ->limit(45),
                TextColumn::make('pickup_date')
                    ->label('Travel date')
                    ->date('j M Y')
                    ->sortable()
                    ->description(fn (Booking $record) => substr((string) $record->pickup_time, 0, 5)),
                TextColumn::make('passengers')
                    ->label('Pax')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('coach.name')
                    ->label('Coach')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Booking::STATUSES),
                SelectFilter::make('coach_id')
                    ->label('Coach')
                    ->relationship('coach', 'name'),
                Filter::make('pickup_date')
                    ->schema([
                        DatePicker::make('travel_from')->label('Travelling from'),
                        DatePicker::make('travel_until')->label('Travelling until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['travel_from'] ?? null, fn ($q, $date) => $q->whereDate('pickup_date', '>=', $date))
                            ->when($data['travel_until'] ?? null, fn ($q, $date) => $q->whereDate('pickup_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Booking $record) => in_array($record->status, [Booking::STATUS_PENDING, Booking::STATUS_CANCELLED]))
                    ->requiresConfirmation()
                    ->modalHeading('Confirm this booking?')
                    ->schema([
                        Checkbox::make('notify')
                            ->label('Email the customer their confirmation')
                            ->default(true),
                    ])
                    ->action(function (Booking $record, array $data) {
                        $record->update(['status' => Booking::STATUS_CONFIRMED]);
                        if ($data['notify'] ?? false) {
                            Mail::to($record->email)->send(new BookingStatusMail($record));
                        }
                    }),
                Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Booking $record) => in_array($record->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED]))
                    ->requiresConfirmation()
                    ->modalHeading('Cancel this booking?')
                    ->schema([
                        Checkbox::make('notify')
                            ->label('Email the customer about the cancellation')
                            ->default(true),
                    ])
                    ->action(function (Booking $record, array $data) {
                        $record->update(['status' => Booking::STATUS_CANCELLED]);
                        if ($data['notify'] ?? false) {
                            Mail::to($record->email)->send(new BookingStatusMail($record));
                        }
                    }),
                Action::make('complete')
                    ->icon('heroicon-o-flag')
                    ->color('info')
                    ->visible(fn (Booking $record) => $record->status === Booking::STATUS_CONFIRMED)
                    ->requiresConfirmation()
                    ->modalHeading('Mark this booking as completed?')
                    ->action(fn (Booking $record) => $record->update(['status' => Booking::STATUS_COMPLETED])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
