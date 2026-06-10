<?php

namespace App\Filament\Resources\FechamentoPagamentos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FechamentoPagamentosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('empresa.id')
                    ->searchable(),
                TextColumn::make('criado_por')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ano')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('total_salarios')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_horas_extras')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_bonus')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_geral')
                    ->numeric()
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
