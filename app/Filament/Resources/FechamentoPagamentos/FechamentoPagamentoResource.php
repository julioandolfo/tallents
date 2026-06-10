<?php

namespace App\Filament\Resources\FechamentoPagamentos;

use App\Filament\Resources\FechamentoPagamentos\Pages\CreateFechamentoPagamento;
use App\Filament\Resources\FechamentoPagamentos\Pages\EditFechamentoPagamento;
use App\Filament\Resources\FechamentoPagamentos\Pages\ListFechamentoPagamentos;
use App\Filament\Resources\FechamentoPagamentos\Schemas\FechamentoPagamentoForm;
use App\Filament\Resources\FechamentoPagamentos\Tables\FechamentoPagamentosTable;
use App\Models\FechamentoPagamento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FechamentoPagamentoResource extends Resource
{
    protected static ?string $model = FechamentoPagamento::class;

    protected static ?string $navigationLabel = 'Fechamentos';
    protected static ?string $modelLabel = 'Fechamento';
    protected static ?string $pluralModelLabel = 'Fechamentos';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestão';
    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FechamentoPagamentoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FechamentoPagamentosTable::configure($table);
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
            'index' => ListFechamentoPagamentos::route('/'),
            'create' => CreateFechamentoPagamento::route('/create'),
            'edit' => EditFechamentoPagamento::route('/{record}/edit'),
        ];
    }
}
