<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Purchase;
use App\PurchaseStatus;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchase extends EditRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('approve')
            ->label( fn(Purchase $record) => $record->status ===PurchaseStatus::COMPLETED ? 'Compra Aprobada':'Aprobar Compra')
            ->icon('heroicon-o-check-circle')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Aprobar Compra')
            ->modalDescription('Advertencia: Al aprobar esta compra, se procederá a crear los movimientos del producto entrante en los almacenes. ¿Desea continuar?')
            ->modalSubmitActionLabel('Sí, aprobar')
            ->action(function (Purchase $record, Action $action){

                if($record->purchaseItems()->count()===0){
                    
                    Notification::make()
                            ->title('Acción no permitida')
                            ->body('La compra debe tener al menos un ítem agregado antes de poder ser aprobada.')
                            ->warning()
                            ->send();
                    
                    $action->halt();
                    return;
                }

                $record->status = PurchaseStatus::COMPLETED;
                $record->save();
                Notification::make()
                ->title('¡Compra aprobada!')
                ->body('La compra ha sido aprobada.')
                ->success()
                ->send();
                $this->refreshForm();
            })
            ->disabled(fn (Purchase $record):bool=>$record->status === PurchaseStatus::COMPLETED)
        ];
    }

    protected function getListeners(): array
    {
        return array_merge(parent::getListeners(),[
            'refreshPurchaseForm'=>'refreshForm'
        ]);
    }

    public function refreshForm(): void
    {
        $this->fillForm();
    }
}
