<?php

namespace App;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StockMovementType: string implements HasColor,HasLabel
{
    //
    case PURCHASE_IN = 'purchase_in';      // Entrada por compra
    case SALE_OUT = 'sale_out';            // Salida por venta
    case ADJUSTMENT_IN = 'adjustment_in';  // Ajuste positivo (corrección, devolución, etc.)
    case ADJUSTMENT_OUT = 'adjustment_out'; // Ajuste negativo (mermas, pérdida, etc.)
    case TRANSFER_IN = 'transfer_in';      // Entrada por transferencia (entre almacenes)
    case TRANSFER_OUT = 'transfer_out';    // Salida por transferencia (entre almacenes)

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PURCHASE_IN => 'Entrada por Compra',
            self::SALE_OUT => 'Salida por Venta',
            self::ADJUSTMENT_IN => 'Ajuste de Entrada',
            self::ADJUSTMENT_OUT => 'Ajuste de Salida',
            self::TRANSFER_IN => 'Transferencia (Entrada)',
            self::TRANSFER_OUT => 'Transferencia (Salida)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PURCHASE_IN=> Color::Green,
            self::SALE_OUT => Color::Red,
            self::ADJUSTMENT_IN => Color::Yellow,
            self::ADJUSTMENT_OUT => Color::Orange,
            self::TRANSFER_IN, self::TRANSFER_OUT => Color::Blue,
            self::TRANSFER_OUT => Color::Purple,
            default => Color::Gray,
        };
    }
}
