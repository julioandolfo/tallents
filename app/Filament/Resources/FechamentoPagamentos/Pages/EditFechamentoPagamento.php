<?php

namespace App\Filament\Resources\FechamentoPagamentos\Pages;

use App\Filament\Resources\FechamentoPagamentos\FechamentoPagamentoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFechamentoPagamento extends EditRecord
{
    protected static string $resource = FechamentoPagamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
