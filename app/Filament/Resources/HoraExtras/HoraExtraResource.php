<?php

namespace App\Filament\Resources\HoraExtras;

use App\Filament\Resources\HoraExtras\Pages\CreateHoraExtra;
use App\Filament\Resources\HoraExtras\Pages\EditHoraExtra;
use App\Filament\Resources\HoraExtras\Pages\ListHoraExtras;
use App\Filament\Resources\HoraExtras\Schemas\HoraExtraForm;
use App\Filament\Resources\HoraExtras\Tables\HoraExtrasTable;
use App\Models\HoraExtra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HoraExtraResource extends Resource
{
    protected static ?string $model = HoraExtra::class;

    protected static ?string $navigationLabel = 'Horas Extras';
    protected static ?string $modelLabel = 'Hora Extra';
    protected static ?string $pluralModelLabel = 'Horas Extras';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestão';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return HoraExtraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HoraExtrasTable::configure($table);
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
            'index' => ListHoraExtras::route('/'),
            'create' => CreateHoraExtra::route('/create'),
            'edit' => EditHoraExtra::route('/{record}/edit'),
        ];
    }
}
