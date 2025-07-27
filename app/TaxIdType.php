<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum TaxIdType: string implements HasLabel
{
    case RUC = 'ruc';
    case DNI = 'dni'; // Documento Nacional de Identidad
    case PASSPORT = 'passport'; // Pasaporte
    case CE = 'ce'; // Carnet de Extranjería (común en Perú)
    case OTHER = 'other'; // Otro tipo de documento

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RUC => 'RUC',
            self::DNI => 'DNI',
            self::PASSPORT => 'Pasaporte',
            self::CE => 'Carnet de Extranjería',
            self::OTHER => 'Otro',
        };
    }
}
