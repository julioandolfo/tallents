<?php

namespace App\Filament\Resources\TipoOcorrencias;

use App\Filament\Resources\TipoOcorrencias\Pages\CreateTipoOcorrencia;
use App\Filament\Resources\TipoOcorrencias\Pages\EditTipoOcorrencia;
use App\Filament\Resources\TipoOcorrencias\Pages\ListTipoOcorrencias;
use App\Filament\Resources\TipoOcorrencias\Schemas\TipoOcorrenciaForm;
use App\Filament\Resources\TipoOcorrencias\Tables\TipoOcorrenciasTable;
use App\Models\TipoOcorrencia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TipoOcorrenciaResource extends Resource
{
    protected static ?string $model = TipoOcorrencia::class;

    protected static ?string $navigationLabel = 'Tipos de Ocorrência';
    protected static ?string $modelLabel = 'Tipo de Ocorrência';
    protected static ?string $pluralModelLabel = 'Tipos de Ocorrência';
    protected static string | \UnitEnum | null $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TipoOcorrenciaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoOcorrenciasTable::configure($table);
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
            'index' => ListTipoOcorrencias::route('/'),
            'create' => CreateTipoOcorrencia::route('/create'),
            'edit' => EditTipoOcorrencia::route('/{record}/edit'),
        ];
    }
}
