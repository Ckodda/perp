<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\StockMovementType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {

        if ($purchase->isDirty('status') && $purchase->status->value === 'completed' && $purchase->getOriginal('status') !== 'completed') {

            DB::transaction(function () use ($purchase) {
                foreach ($purchase->purchaseItems as $item) {
                    StockMovement::create([
                        'company_id' => $purchase->company_id,
                        'product_id' => $item->product_id,
                        'warehouse_id' => null,
                        'type' => StockMovementType::PURCHASE_IN,
                        'quantity' => $item->quantity,
                        'reference' => 'Compra #' . $purchase->invoice_number,
                        'notes' => 'Entrada por compra completada. ID de Compra: ' . $purchase->id,
                        'user_id' => Auth::user()->id ?? null,
                    ]);
                }
            });
        }
    }

    /**
     * Handle the Purchase "deleted" event.
     */
    public function deleted(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "restored" event.
     */
    public function restored(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "force deleted" event.
     */
    public function forceDeleted(Purchase $purchase): void
    {
        //
    }
}
