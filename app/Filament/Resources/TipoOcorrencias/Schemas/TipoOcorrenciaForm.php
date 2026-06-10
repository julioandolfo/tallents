<?php

namespace App\Filament\Resources\TipoOcorrencias\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TipoOcorrenciaForm
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
                TextInput::make('tipo'),
                TextInput::make('gravidade'),
                Toggle::make('ativo')
                    ->required(),
            ]);
    }
}
