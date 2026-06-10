<?php

namespace App\Filament\Resources\TipoBonuses;

use App\Filament\Resources\TipoBonuses\Pages\CreateTipoBonus;
use App\Filament\Resources\TipoBonuses\Pages\EditTipoBonus;
use App\Filament\Resources\TipoBonuses\Pages\ListTipoBonuses;
use App\Filament\Resources\TipoBonuses\Schemas\TipoBonusForm;
use App\Filament\Resources\TipoBonuses\Tables\TipoBonusesTable;
use App\Models\TipoBonus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TipoBonusResource extends Resource
{
    protected static ?string $model = TipoBonus::class;

    protected static ?string $navigationLabel = 'Tipos de Bônus';
    protected static ?string $modelLabel = 'Tipo de Bônus';
    protected static ?string $pluralModelLabel = 'Tipos de Bônus';
    protected static string | \UnitEnum | null $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TipoBonusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoBonusesTable::configure($table);
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
            'index' => ListTipoBonuses::route('/'),
            'create' => CreateTipoBonus::route('/create'),
            'edit' => EditTipoBonus::route('/{record}/edit'),
        ];
    }
}
