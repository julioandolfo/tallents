<?php

namespace App\Filament\Resources\Ocorrencias\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OcorrenciaForm
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
                Select::make('tipo_ocorrencia_id')
                    ->relationship('tipoOcorrencia', 'id'),
                TextInput::make('registrado_por')
                    ->numeric(),
                TextInput::make('titulo'),
                TextInput::make('gravidade'),
                Textarea::make('descricao')
                    ->columnSpanFull(),
                DatePicker::make('data_ocorrencia')
                    ->required(),
                Toggle::make('notificar_colaborador')
                    ->required(),
            ]);
    }
}
