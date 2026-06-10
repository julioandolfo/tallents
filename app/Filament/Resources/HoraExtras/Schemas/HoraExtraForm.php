<?php

namespace App\Filament\Resources\HoraExtras\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HoraExtraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('empresa_id')
                    ->relationship('empresa', 'id')
                    ->required(),
                Select::make('colaborador_id')
                    ->relationship('colaborador', 'id')
                    ->required(),
                TextInput::make('registrado_por')
                    ->numeric(),
                DatePicker::make('data')
                    ->required(),
                TextInput::make('horas')
                    ->required()
                    ->numeric(),
                TextInput::make('percentual')
                    ->required()
                    ->numeric()
                    ->default(50),
                TextInput::make('valor')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('motivo'),
                TextInput::make('observacao'),
                TextInput::make('status')
                    ->required()
                    ->default('pendente'),
                Toggle::make('aprovado')
                    ->required(),
            ]);
    }
}
