<?php

namespace App\Filament\Resources\Usuarios\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UsuarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('cpf'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('role')
                    ->required()
                    ->default('ADMIN'),
                Select::make('empresa_id')
                    ->relationship('empresa', 'id'),
                Select::make('setor_id')
                    ->relationship('setor', 'id'),
                TextInput::make('foto'),
                Toggle::make('ativo')
                    ->required(),
                DateTimePicker::make('last_login_at'),
                DateTimePicker::make('email_verified_at'),
            ]);
    }
}
