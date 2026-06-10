<?php

namespace App\Filament\Resources\Promocaos\Pages;

use App\Filament\Resources\Promocaos\PromocaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPromocao extends EditRecord
{
    protected static string $resource = PromocaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
