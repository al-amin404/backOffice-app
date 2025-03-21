<?php

namespace App\Filament\Resources\PassportResource\Pages;

use App\Filament\Resources\PassportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPassport extends ViewRecord
{
    protected static string $resource = PassportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
