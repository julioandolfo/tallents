<?php

namespace App\Filament\Resources\TipoBonuses\Pages;

use App\Filament\Resources\TipoBonuses\TipoBonusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoBonuses extends ListRecords
{
    protected static string $resource = TipoBonusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
