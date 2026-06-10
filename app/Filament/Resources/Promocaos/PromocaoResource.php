<?php

namespace App\Filament\Resources\Promocaos;

use App\Filament\Resources\Promocaos\Pages\CreatePromocao;
use App\Filament\Resources\Promocaos\Pages\EditPromocao;
use App\Filament\Resources\Promocaos\Pages\ListPromocaos;
use App\Filament\Resources\Promocaos\Schemas\PromocaoForm;
use App\Filament\Resources\Promocaos\Tables\PromocaosTable;
use App\Models\Promocao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PromocaoResource extends Resource
{
    protected static ?string $model = Promocao::class;

    protected static ?string $navigationLabel = 'Promoções';
    protected static ?string $modelLabel = 'Promoção';
    protected static ?string $pluralModelLabel = 'Promoções';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestão';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PromocaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromocaosTable::configure($table);
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
            'index' => ListPromocaos::route('/'),
            'create' => CreatePromocao::route('/create'),
            'edit' => EditPromocao::route('/{record}/edit'),
        ];
    }
}
