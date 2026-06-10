<?php

namespace App\Filament\Resources\HoraExtras\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HoraExtrasTable
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
                TextColumn::make('data')
                    ->date()
                    ->sortable(),
                TextColumn::make('horas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('percentual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('valor')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('motivo')
                    ->searchable(),
                TextColumn::make('observacao')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('aprovado')
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
