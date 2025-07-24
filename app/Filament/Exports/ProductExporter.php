<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('company.id'),
            ExportColumn::make('name'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('unit_of_measure'),
            ExportColumn::make('purchase_price'),
            ExportColumn::make('sale_price'),
            ExportColumn::make('stock'),
            ExportColumn::make('min_stock_alert'),
            ExportColumn::make('description'),
            ExportColumn::make('image'),
            ExportColumn::make('weight'),
            ExportColumn::make('is_active'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('weight_unit'),
            ExportColumn::make('length'),
            ExportColumn::make('width'),
            ExportColumn::make('height'),
            ExportColumn::make('dimension_unit'),
            ExportColumn::make('purchaseCurrency.name'),
            ExportColumn::make('saleCurrency.name'),
            ExportColumn::make('productCategory.name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
