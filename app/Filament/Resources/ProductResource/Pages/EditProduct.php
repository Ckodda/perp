<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->modalHeading('Eliminar Producto')
                ->modalDescription('Advertencia: Esta acción eliminará los datos del producto, no se podrá deshacer ni crear un nuevo producto con el mismo SKU. ¿Está seguro de continuar con la eliminación?'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return ProductResource::getUrl('view',['record'=>$this->record->getKey()]);
    }

    
}
