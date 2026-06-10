<?php

namespace App\Filament\Resources\TipoOcorrencias\Pages;

use App\Filament\Resources\TipoOcorrencias\TipoOcorrenciaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoOcorrencias extends ListRecords
{
    protected static string $resource = TipoOcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
