<?php

namespace App\Filament\Resources\NivelHierarquicos\Pages;

use App\Filament\Resources\NivelHierarquicos\NivelHierarquicoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNivelHierarquico extends EditRecord
{
    protected static string $resource = NivelHierarquicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
