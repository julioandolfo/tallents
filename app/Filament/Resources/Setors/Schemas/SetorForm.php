<?php

namespace App\Filament\Resources\Setors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SetorForm
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
                Toggle::make('ativo')
                    ->required(),
            ]);
    }
}
