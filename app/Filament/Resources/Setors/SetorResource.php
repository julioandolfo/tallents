<?php

namespace App\Filament\Resources\Setors;

use App\Filament\Resources\Setors\Pages\CreateSetor;
use App\Filament\Resources\Setors\Pages\EditSetor;
use App\Filament\Resources\Setors\Pages\ListSetors;
use App\Filament\Resources\Setors\Schemas\SetorForm;
use App\Filament\Resources\Setors\Tables\SetorsTable;
use App\Models\Setor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SetorResource extends Resource
{
    protected static ?string $model = Setor::class;

    protected static ?string $navigationLabel = 'Setores';
    protected static ?string $modelLabel = 'Setor';
    protected static ?string $pluralModelLabel = 'Setores';
    protected static string | \UnitEnum | null $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SetorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SetorsTable::configure($table);
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
            'index' => ListSetors::route('/'),
            'create' => CreateSetor::route('/create'),
            'edit' => EditSetor::route('/{record}/edit'),
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
