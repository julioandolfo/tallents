<?php

namespace App\Filament\Resources\Promocaos\Pages;

use App\Filament\Resources\Promocaos\PromocaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPromocaos extends ListRecords
{
    protected static string $resource = PromocaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
