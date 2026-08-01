<?php

namespace App\Filament\Resources\CoachHireLocations;

use App\Filament\Resources\CoachHireLocations\Pages\CreateCoachHireLocation;
use App\Filament\Resources\CoachHireLocations\Pages\EditCoachHireLocation;
use App\Filament\Resources\CoachHireLocations\Pages\ListCoachHireLocations;
use App\Filament\Resources\CoachHireLocations\Schemas\CoachHireLocationForm;
use App\Filament\Resources\CoachHireLocations\Tables\CoachHireLocationsTable;
use App\Models\CoachHireLocation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CoachHireLocationResource extends Resource
{
    protected static ?string $model = CoachHireLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Coach Hire Locations';

    public static function form(Schema $schema): Schema
    {
        return CoachHireLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoachHireLocationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoachHireLocations::route('/'),
            'create' => CreateCoachHireLocation::route('/create'),
            'edit' => EditCoachHireLocation::route('/{record}/edit'),
        ];
    }
}
