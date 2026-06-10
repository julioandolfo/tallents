<?php

namespace App\Filament\Resources\Empresas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmpresaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required(),
                TextInput::make('razao_social'),
                TextInput::make('cnpj'),
                TextInput::make('telefone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('site'),
                TextInput::make('endereco'),
                TextInput::make('cep'),
                TextInput::make('logradouro'),
                TextInput::make('numero'),
                TextInput::make('complemento'),
                TextInput::make('bairro'),
                TextInput::make('cidade'),
                TextInput::make('estado'),
                TextInput::make('percentual_hora_extra')
                    ->required()
                    ->numeric()
                    ->default(50),
                TextInput::make('logo'),
                Toggle::make('ativa')
                    ->required(),
            ]);
    }
}
