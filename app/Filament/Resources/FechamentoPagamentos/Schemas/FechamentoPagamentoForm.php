<?php

namespace App\Filament\Resources\FechamentoPagamentos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FechamentoPagamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('empresa_id')
                    ->relationship('empresa', 'id')
                    ->required(),
                TextInput::make('criado_por')
                    ->numeric(),
                TextInput::make('mes')
                    ->required()
                    ->numeric(),
                TextInput::make('ano')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('ABERTO'),
                TextInput::make('total_salarios')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_horas_extras')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_bonus')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_geral')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('observacoes')
                    ->columnSpanFull(),
            ]);
    }
}
