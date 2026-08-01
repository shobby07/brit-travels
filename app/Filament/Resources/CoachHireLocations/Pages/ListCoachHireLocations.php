<?php

namespace App\Filament\Resources\CoachHireLocations\Pages;

use App\Filament\Resources\CoachHireLocations\CoachHireLocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoachHireLocations extends ListRecords
{
    protected static string $resource = CoachHireLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
