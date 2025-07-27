<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplier extends ViewRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->button()
            ->label('')
            ->color('gray')
            ->url(SupplierResource::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            EditAction::make()
            ->icon('heroicon-o-pencil'),
            DeleteAction::make()
        ];
    }
}
