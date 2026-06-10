<?php

namespace App\Filament\Resources\TipoBonuses\Pages;

use App\Filament\Resources\TipoBonuses\TipoBonusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoBonus extends EditRecord
{
    protected static string $resource = TipoBonusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
