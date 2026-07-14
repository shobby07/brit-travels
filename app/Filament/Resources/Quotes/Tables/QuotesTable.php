<?php

namespace App\Filament\Resources\Quotes\Tables;

use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotesTable
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
                    ->formatStateUsing(fn (string $state) => Quote::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        Quote::STATUS_NEW => 'warning',
                        Quote::STATUS_RESPONDED => 'success',
                        Quote::STATUS_CLOSED => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('name')
                    ->label('Customer')
                    ->searchable()
                    ->description(fn (Quote $record) => $record->phone),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('pickup_location')
                    ->label('Route')
                    ->searchable()
                    ->formatStateUsing(fn (Quote $record) => $record->pickup_location.' → '.$record->dropoff_location)
                    ->limit(45),
                TextColumn::make('pickup_date')
                    ->label('Travel date')
                    ->date('j M Y')
                    ->sortable()
                    ->placeholder('Flexible'),
                TextColumn::make('passengers')
                    ->label('Pax')
                    ->numeric()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Quote::STATUSES),
            ])
            ->recordActions([
                Action::make('markResponded')
                    ->label('Responded')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Quote $record) => $record->status === Quote::STATUS_NEW)
                    ->requiresConfirmation()
                    ->modalHeading('Mark this quote as responded?')
                    ->action(fn (Quote $record) => $record->update(['status' => Quote::STATUS_RESPONDED])),
                Action::make('close')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->visible(fn (Quote $record) => $record->status !== Quote::STATUS_CLOSED)
                    ->requiresConfirmation()
                    ->modalHeading('Close this quote?')
                    ->action(fn (Quote $record) => $record->update(['status' => Quote::STATUS_CLOSED])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
