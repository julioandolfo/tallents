<?php

namespace App\Filament\Resources\Colaboradors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ColaboradorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('empresa.id')
                    ->searchable(),
                TextColumn::make('setor.id')
                    ->searchable(),
                TextColumn::make('cargo.id')
                    ->searchable(),
                TextColumn::make('nivelHierarquico.id')
                    ->searchable(),
                TextColumn::make('lider.id')
                    ->searchable(),
                TextColumn::make('nome')
                    ->searchable(),
                TextColumn::make('cpf')
                    ->searchable(),
                TextColumn::make('rg')
                    ->searchable(),
                TextColumn::make('pis')
                    ->searchable(),
                TextColumn::make('ctps')
                    ->searchable(),
                TextColumn::make('matricula')
                    ->searchable(),
                TextColumn::make('data_nascimento')
                    ->date()
                    ->sortable(),
                TextColumn::make('sexo')
                    ->searchable(),
                TextColumn::make('estado_civil')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_pessoal')
                    ->searchable(),
                TextColumn::make('email_login')
                    ->searchable(),
                TextColumn::make('telefone')
                    ->searchable(),
                TextColumn::make('celular')
                    ->searchable(),
                TextColumn::make('tipo_contrato')
                    ->searchable(),
                TextColumn::make('carga_horaria')
                    ->searchable(),
                TextColumn::make('data_admissao')
                    ->date()
                    ->sortable(),
                TextColumn::make('data_demissao')
                    ->date()
                    ->sortable(),
                TextColumn::make('salario')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('foto')
                    ->searchable(),
                TextColumn::make('senha_hash')
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
