<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockMovement extends ViewRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->button()
            ->label('')
            ->color('gray')
            ->url(StockMovementResource::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            EditAction::make()
            ->icon('heroicon-o-pencil'),
        ];
    }
}
