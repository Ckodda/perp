<?php

namespace App\Filament\Resources\WarehouseResource\Pages;

use App\Filament\Resources\WarehouseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWarehouse extends ViewRecord
{
    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->button()
            ->label('')
            ->color('gray')
            ->url(WarehouseResource::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            EditAction::make()
            ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->modalHeading('Eliminar Almacén')
                ->label('')
                ->modalDescription('Advertencia: Esta acción eliminará los datos del almacén, no se podrá deshacer ni crear un nuevo almacén con el mismo nombre. ¿Está seguro de continuar con la eliminación?')->icon('heroicon-o-trash'),
        ];
    }
}
