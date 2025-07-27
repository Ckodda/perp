<?php

namespace App;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PurchaseStatus: string implements HasLabel, HasColor
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned'; // Puedes añadir más si lo necesitas, como 'returned' o 'partially_received'
    case PARTIALLY_RECEIVED = 'partially_received';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::COMPLETED => 'Completada',
            self::CANCELLED => 'Cancelada',
            self::RETURNED => 'Devuelta',
            self::PARTIALLY_RECEIVED => 'Recibida Parcialmente',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => Color::Yellow, // O Color::Warning en Filament v3
            self::COMPLETED => Color::Green,
            self::CANCELLED => Color::Red,
            self::RETURNED => Color::Blue,
            self::PARTIALLY_RECEIVED => Color::Orange,
        };
    }
}