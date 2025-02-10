<?php

namespace App\Filament\Resources\StaffResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\StaffResource;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('View ID')
                ->label('View ID')
                ->icon('heroicon-o-identification')
                ->url(fn($record) => route('staffID', ['staff_id' => $record->staff_id]))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make(),
        ];
    }
}
