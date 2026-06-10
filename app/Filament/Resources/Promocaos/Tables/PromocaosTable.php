<?php

namespace App\Filament\Resources\Promocaos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromocaosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('empresa.id')
                    ->searchable(),
                TextColumn::make('colaborador.id')
                    ->searchable(),
                TextColumn::make('registrado_por')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->searchable(),
                TextColumn::make('cargoAnterior.id')
                    ->searchable(),
                TextColumn::make('cargoNovo.id')
                    ->searchable(),
                TextColumn::make('salario_anterior')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('salario_novo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('data_promocao')
                    ->date()
                    ->sortable(),
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
