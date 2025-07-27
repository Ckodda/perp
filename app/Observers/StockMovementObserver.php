<?php

namespace App\Observers;

use App\Models\StockMovement;
use App\StockMovementType;
use Illuminate\Support\Facades\Auth;

class StockMovementObserver
{

    public function creating(StockMovement $stockMovement): void
    {
        //
        if (empty($stockMovement->company_id)) {
            if ($stockMovement->product) {
                $stockMovement->company_id = $stockMovement->product->company_id;
            } elseif (Auth::check() && Auth::user()->company_id) {
                $stockMovement->company_id = Auth::user()->company_id;
            }
        }
        if (empty($stockMovement->user_id) && Auth::check()) {
            $stockMovement->user_id = Auth::user()->id;
        }
    }

    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $stockMovement): void
    {
        $product = $stockMovement->product;
        if ($product) {
            switch ($stockMovement->type) {
                case StockMovementType::PURCHASE_IN:
                case StockMovementType::ADJUSTMENT_IN:
                case StockMovementType::TRANSFER_IN:
                    $product->increment('stock', $stockMovement->quantity);
                    break;
                case StockMovementType::SALE_OUT:
                case StockMovementType::ADJUSTMENT_OUT:
                case StockMovementType::TRANSFER_OUT:
                    $product->decrement('stock', $stockMovement->quantity);
                    break;
            }
        }
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
        $product = $stockMovement->product;
        if ($product) {
            switch ($stockMovement->type) {
                case StockMovementType::PURCHASE_IN:
                case StockMovementType::ADJUSTMENT_IN:
                case StockMovementType::TRANSFER_IN:
                    $product->decrement('stock', $stockMovement->quantity);
                    break;
                case StockMovementType::SALE_OUT:
                case StockMovementType::ADJUSTMENT_OUT:
                case StockMovementType::TRANSFER_OUT:
                    $product->increment('stock', $stockMovement->quantity);
                    break;
            }
        }
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
        $product = $stockMovement->product;
        if ($product) {
            switch ($stockMovement->type) {
                case StockMovementType::PURCHASE_IN:
                case StockMovementType::ADJUSTMENT_IN:
                case StockMovementType::TRANSFER_IN:
                    $product->increment('stock', $stockMovement->quantity);
                    break;
                case StockMovementType::SALE_OUT:
                case StockMovementType::ADJUSTMENT_OUT:
                case StockMovementType::TRANSFER_OUT:
                    $product->decrement('stock', $stockMovement->quantity);
                    break;
            }
        }
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }
}
