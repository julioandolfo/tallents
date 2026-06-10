<?php

namespace App\Filament\Resources\Promocaos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PromocaoForm
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
                TextInput::make('tipo')
                    ->required()
                    ->default('REAJUSTE'),
                Select::make('cargo_anterior_id')
                    ->relationship('cargoAnterior', 'id'),
                Select::make('cargo_novo_id')
                    ->relationship('cargoNovo', 'id'),
                TextInput::make('salario_anterior')
                    ->required()
                    ->numeric(),
                TextInput::make('salario_novo')
                    ->required()
                    ->numeric(),
                DatePicker::make('data_promocao')
                    ->required(),
                Textarea::make('motivo')
                    ->columnSpanFull(),
            ]);
    }
}
