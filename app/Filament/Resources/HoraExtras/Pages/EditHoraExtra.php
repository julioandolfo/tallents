<?php

namespace App\Filament\Resources\HoraExtras\Pages;

use App\Filament\Resources\HoraExtras\HoraExtraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHoraExtra extends EditRecord
{
    protected static string $resource = HoraExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
