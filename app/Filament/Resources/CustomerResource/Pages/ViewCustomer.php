<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->button()
            ->label('')
            ->color('gray')
            ->url(CustomerResource::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            EditAction::make()
            ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->modalHeading('Eliminar Cliente')
                ->label('')
                ->modalDescription('Advertencia: Esta acción eliminará los datos del cliente, no se podrá deshacer ni crear una nueva empresa con el mismo número de Documento (DNI/RUC). ¿Está seguro de continuar con la eliminación?')->icon('heroicon-o-trash'),
        ];
    }
}
