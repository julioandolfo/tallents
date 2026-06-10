<?php

namespace App\Filament\Resources\Empresas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EmpresasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->searchable(),
                TextColumn::make('razao_social')
                    ->searchable(),
                TextColumn::make('cnpj')
                    ->searchable(),
                TextColumn::make('telefone')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('site')
                    ->searchable(),
                TextColumn::make('endereco')
                    ->searchable(),
                TextColumn::make('cep')
                    ->searchable(),
                TextColumn::make('logradouro')
                    ->searchable(),
                TextColumn::make('numero')
                    ->searchable(),
                TextColumn::make('complemento')
                    ->searchable(),
                TextColumn::make('bairro')
                    ->searchable(),
                TextColumn::make('cidade')
                    ->searchable(),
                TextColumn::make('estado')
                    ->searchable(),
                TextColumn::make('percentual_hora_extra')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('logo')
                    ->searchable(),
                IconColumn::make('ativa')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
