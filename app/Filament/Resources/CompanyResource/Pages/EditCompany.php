<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->modalHeading('Eliminar Empresa')
                ->modalDescription('Advertencia: Esta acción eliminará los datos de la empresa, no se podrá deshacer ni crear una nueva empresa con el mismo número de RUC. ¿Está seguro de continuar con la eliminación?'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return CompanyResource::getUrl('view',['record'=>$this->record->getKey()]);
    }
}
