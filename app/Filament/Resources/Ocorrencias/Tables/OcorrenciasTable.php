<?php

namespace App\Filament\Resources\Ocorrencias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OcorrenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('empresa.id')
                    ->searchable(),
                TextColumn::make('colaborador.id')
                    ->searchable(),
                TextColumn::make('tipoOcorrencia.id')
                    ->searchable(),
                TextColumn::make('registrado_por')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('titulo')
                    ->searchable(),
                TextColumn::make('gravidade')
                    ->searchable(),
                TextColumn::make('data_ocorrencia')
                    ->date()
                    ->sortable(),
                IconColumn::make('notificar_colaborador')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
