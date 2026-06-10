<?php

namespace App\Filament\Resources\Colaboradors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ColaboradorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('empresa_id')
                    ->relationship('empresa', 'id')
                    ->required(),
                Select::make('setor_id')
                    ->relationship('setor', 'id'),
                Select::make('cargo_id')
                    ->relationship('cargo', 'id'),
                Select::make('nivel_hierarquico_id')
                    ->relationship('nivelHierarquico', 'id'),
                Select::make('lider_id')
                    ->relationship('lider', 'id'),
                TextInput::make('nome')
                    ->required(),
                TextInput::make('cpf'),
                TextInput::make('rg'),
                TextInput::make('pis'),
                TextInput::make('ctps'),
                TextInput::make('matricula'),
                DatePicker::make('data_nascimento'),
                TextInput::make('sexo'),
                TextInput::make('estado_civil'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('email_pessoal')
                    ->email(),
                TextInput::make('email_login')
                    ->email(),
                TextInput::make('telefone')
                    ->tel(),
                TextInput::make('celular'),
                TextInput::make('tipo_contrato')
                    ->default('CLT'),
                TextInput::make('carga_horaria'),
                DatePicker::make('data_admissao'),
                DatePicker::make('data_demissao'),
                TextInput::make('salario')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('ATIVO'),
                TextInput::make('foto'),
                TextInput::make('senha_hash'),
                TextInput::make('cep'),
                TextInput::make('logradouro'),
                TextInput::make('numero'),
                TextInput::make('complemento'),
                TextInput::make('bairro'),
                TextInput::make('cidade'),
                TextInput::make('estado'),
                Textarea::make('observacoes')
                    ->columnSpanFull(),
            ]);
    }
}
