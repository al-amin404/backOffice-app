<?php

namespace App\Filament\Resources\PassportResource\Pages;

use App\Filament\Resources\PassportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPassports extends ListRecords
{
    protected static string $resource = PassportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
