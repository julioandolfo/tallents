<?php

namespace App\Filament\Resources\Colaboradors;

use App\Filament\Resources\Colaboradors\Pages\CreateColaborador;
use App\Filament\Resources\Colaboradors\Pages\EditColaborador;
use App\Filament\Resources\Colaboradors\Pages\ListColaboradors;
use App\Filament\Resources\Colaboradors\Schemas\ColaboradorForm;
use App\Filament\Resources\Colaboradors\Tables\ColaboradorsTable;
use App\Models\Colaborador;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ColaboradorResource extends Resource
{
    protected static ?string $model = Colaborador::class;

    protected static ?string $navigationLabel = 'Colaboradores';
    protected static ?string $modelLabel = 'Colaborador';
    protected static ?string $pluralModelLabel = 'Colaboradores';
    protected static string | \UnitEnum | null $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ColaboradorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ColaboradorsTable::configure($table);
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
            'index' => ListColaboradors::route('/'),
            'create' => CreateColaborador::route('/create'),
            'edit' => EditColaborador::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
