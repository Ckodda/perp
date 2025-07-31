<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CustomerType: string implements HasColor, HasIcon, HasLabel
{
    case INDIVIDUAL = 'individual';
    case COMPANY = 'company';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Persona',
            self::COMPANY => 'Empresa',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INDIVIDUAL => 'success',
            self::COMPANY => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::INDIVIDUAL => 'heroicon-o-user',
            self::COMPANY => 'heroicon-o-building-office-2',
        };
    }
}

