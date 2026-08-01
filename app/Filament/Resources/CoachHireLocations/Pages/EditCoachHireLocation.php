<?php

namespace App\Filament\Resources\CoachHireLocations\Pages;

use App\Filament\Resources\CoachHireLocations\CoachHireLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoachHireLocation extends EditRecord
{
    protected static string $resource = CoachHireLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
