<?php

namespace App\Filament\Resources\TipoOcorrencias\Pages;

use App\Filament\Resources\TipoOcorrencias\TipoOcorrenciaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoOcorrencia extends EditRecord
{
    protected static string $resource = TipoOcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
