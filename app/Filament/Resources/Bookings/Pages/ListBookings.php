<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, [
                            'Reference', 'Status', 'Trip type', 'Pickup', 'Drop-off',
                            'Pickup date', 'Pickup time', 'Return date', 'Return time',
                            'Passengers', 'Coach', 'Name', 'Email', 'Phone', 'Notes', 'Received',
                        ]);
                        Booking::with('coach')->latest()->chunk(200, function ($bookings) use ($out) {
                            foreach ($bookings as $b) {
                                fputcsv($out, [
                                    $b->reference,
                                    $b->status,
                                    $b->trip_type === 'round_trip' ? 'Round trip' : 'One way',
                                    $b->pickup_location,
                                    $b->dropoff_location,
                                    $b->pickup_date?->toDateString(),
                                    substr((string) $b->pickup_time, 0, 5),
                                    $b->return_date?->toDateString(),
                                    $b->return_time ? substr((string) $b->return_time, 0, 5) : null,
                                    $b->passengers,
                                    $b->coach?->name,
                                    $b->name,
                                    $b->email,
                                    $b->phone,
                                    $b->notes,
                                    $b->created_at->toDateTimeString(),
                                ]);
                            }
                        });
                        fclose($out);
                    }, 'bookings-'.now()->format('Y-m-d').'.csv');
                }),
            CreateAction::make(),
        ];
    }
}
