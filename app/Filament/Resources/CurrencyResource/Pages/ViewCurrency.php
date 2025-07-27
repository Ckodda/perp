<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCurrency extends ViewRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->button()
            ->label('')
            ->color('gray')
            ->url(CurrencyResource::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            EditAction::make()
            ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->modalHeading('Eliminar Moneda')
                ->label('')
                ->modalDescription('Advertencia: Esta acción eliminará los datos de la moneda, no se podrá deshacer ni crear una nueva moneda con el mismo nombre y código ISO. ¿Está seguro de continuar con la eliminación?')->icon('heroicon-o-trash'),
        ];
    }
}
