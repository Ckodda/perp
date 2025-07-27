<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->button()
            ->label('')
            ->color('gray')
            ->url(ProductResource::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            EditAction::make()
            ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->modalHeading('Eliminar Producto')
                ->label('')
                ->modalDescription('Advertencia: Esta acción eliminará los datos del producto, no se podrá deshacer ni crear un nuevo producto con el mismo SKU. ¿Está seguro de continuar con la eliminación?')->icon('heroicon-o-trash'),
        ];
    }
}
