<?php

namespace App;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum JournalType: string implements HasLabel, HasColor
{
    case SALES = 'sales';
    case PURCHASE = 'purchase';
    case CREDIT_NOTE = 'credit_note';
    case DEBIT_NOTE = 'debit_note';
    case CASH = 'cash';
    case BANK = 'bank';
    case JOURNAL_ENTRY = 'journal_entry'; 

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SALES => 'Ventas',
            self::PURCHASE => 'Compras',
            self::CREDIT_NOTE => 'Notas de Crédito',
            self::DEBIT_NOTE => 'Notas de Débito',
            self::CASH => 'Caja',
            self::BANK => 'Banco',
            self::JOURNAL_ENTRY => 'Asiento Contable',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SALES => Color::Green,
            self::PURCHASE => Color::Orange,
            self::CREDIT_NOTE => Color::Blue,
            self::DEBIT_NOTE => Color::Red,
            self::CASH => Color::Yellow,
            self::BANK => Color::Cyan,
            self::JOURNAL_ENTRY => Color::Gray,
        };
    }
}