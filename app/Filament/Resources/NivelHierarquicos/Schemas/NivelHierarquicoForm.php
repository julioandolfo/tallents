<?php

namespace App\Filament\Resources\NivelHierarquicos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class NivelHierarquicoForm
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
                TextInput::make('nivel')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('descricao')
                    ->columnSpanFull(),
            ]);
    }
}
