<?php

namespace App\Filament\Resources\NivelHierarquicos\Pages;

use App\Filament\Resources\NivelHierarquicos\NivelHierarquicoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNivelHierarquicos extends ListRecords
{
    protected static string $resource = NivelHierarquicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
