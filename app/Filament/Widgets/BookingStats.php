<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Quote;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Pending bookings', Booking::where('status', Booking::STATUS_PENDING)->count())
                ->description('Awaiting your confirmation')
                ->color('warning'),
            Stat::make('Bookings this month', Booking::whereBetween('created_at', [now()->startOfMonth(), now()])->count())
                ->description(now()->format('F Y'))
                ->color('success'),
            Stat::make('New quote requests', Quote::where('status', Quote::STATUS_NEW)->count())
                ->description('Waiting for a price')
                ->color('info'),
            Stat::make('Upcoming trips', Booking::where('status', Booking::STATUS_CONFIRMED)->whereDate('pickup_date', '>=', today())->count())
                ->description('Confirmed, travelling soon')
                ->color('primary'),
        ];
    }
}
