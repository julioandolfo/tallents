<?php

namespace App\Filament\Resources\TipoBonuses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TipoBonusForm
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
                TextInput::make('descricao'),
                TextInput::make('tipo_calculo'),
                TextInput::make('percentual')
                    ->numeric(),
                TextInput::make('fixo')
                    ->numeric(),
                Toggle::make('ativo')
                    ->required(),
            ]);
    }
}
