<?php

namespace App\Filament\Resources\FechamentoPagamentos\Pages;

use App\Filament\Resources\FechamentoPagamentos\FechamentoPagamentoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFechamentoPagamentos extends ListRecords
{
    protected static string $resource = FechamentoPagamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
