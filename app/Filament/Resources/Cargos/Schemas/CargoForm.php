<?php

namespace App\Filament\Resources\Cargos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CargoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('empresa_id')
                    ->relationship('empresa', 'id')
                    ->required(),
                TextInput::make('nome')
                    ->required(),
                Textarea::make('descricao')
                    ->columnSpanFull(),
                Select::make('nivel_hierarquico_id')
                    ->relationship('nivelHierarquico', 'id'),
                TextInput::make('salario_base')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('salario_maximo')
                    ->numeric(),
                Toggle::make('ativo')
                    ->required(),
            ]);
    }
}
