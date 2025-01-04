<?php

namespace App\Filament\Resources\PassportResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PassportResource;
use App\Models\Passport;
use Filament\Resources\Components\Tab;

class ListPassports extends ListRecords
{
    protected static string $resource = PassportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'New' => Tab::make()
                            ->query(fn ($query) => $query->where('status', 'Received'))
                            ->badge(fn () => ($count = Passport::where('status', 'Received')->count()) ? $count : null),
            'GCC Fit' => Tab::make()
                                ->query(fn ($query) => $query->where('status', 'GCC Fit'))
                                ->badge(fn () => ($count = Passport::where('status', 'GCC Fit')->count()) ? $count : null),
            'processing' => Tab::make()
                                    ->query(fn ($query) => $query->where('status', 'Processing'))
                                    ->badge(fn () => ($count = Passport::where('status', 'Processing')->count()) ? $count : null),
            'On-hold' => Tab::make()
                                ->query(fn ($query) => $query->where('status', 'On-hold'))
                                ->badge(fn () => ($count = Passport::where('status', 'On-hold')->count()) ? $count : null),
            'Mofa Done' => Tab::make()
                                    ->query(fn ($query) => $query->where('status', 'Mofa Done'))
                                    ->badge(fn () => ($count = Passport::where('status', 'Mofa Done')->count()) ? $count : null),
            'Sent for Embassy' => Tab::make()
                                            ->query(fn ($query) => $query->where('status', 'Sent for Embassy'))
                                            ->badge(fn () => ($count = Passport::where('status', 'Sent for Embassy')->count()) ? $count : null),
            'BMET Processing' => Tab::make()
                                        ->query(fn ($query) => $query->where('status', 'BMET Processing'))
                                        ->badge(fn () => ($count = Passport::where('status', 'BMET Processing')->count()) ? $count : null),
        ];
    }
}
