<?php

namespace App\Filament\Resources\NivelHierarquicos;

use App\Filament\Resources\NivelHierarquicos\Pages\CreateNivelHierarquico;
use App\Filament\Resources\NivelHierarquicos\Pages\EditNivelHierarquico;
use App\Filament\Resources\NivelHierarquicos\Pages\ListNivelHierarquicos;
use App\Filament\Resources\NivelHierarquicos\Schemas\NivelHierarquicoForm;
use App\Filament\Resources\NivelHierarquicos\Tables\NivelHierarquicosTable;
use App\Models\NivelHierarquico;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NivelHierarquicoResource extends Resource
{
    protected static ?string $model = NivelHierarquico::class;

    protected static ?string $navigationLabel = 'Níveis Hierárquicos';
    protected static ?string $modelLabel = 'Nível Hierárquico';
    protected static ?string $pluralModelLabel = 'Níveis Hierárquicos';
    protected static string | \UnitEnum | null $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return NivelHierarquicoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NivelHierarquicosTable::configure($table);
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
            'index' => ListNivelHierarquicos::route('/'),
            'create' => CreateNivelHierarquico::route('/create'),
            'edit' => EditNivelHierarquico::route('/{record}/edit'),
        ];
    }
}
