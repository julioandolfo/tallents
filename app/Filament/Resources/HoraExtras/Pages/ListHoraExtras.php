<?php

namespace App\Filament\Resources\HoraExtras\Pages;

use App\Filament\Resources\HoraExtras\HoraExtraResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHoraExtras extends ListRecords
{
    protected static string $resource = HoraExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
