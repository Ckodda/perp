<?php

namespace App\Filament\Resources\PurchaseResource\RelationManagers;

use App\Models\Currency;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseItems';

    protected static ?string $recordTitleAttribute = 'product.name';
    protected static ?string $modelLabel = 'Ítem de Compra';
    protected static ?string $pluralModelLabel = 'Ítems de Compra';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship(
                                'product',
                                'name',
                                fn(Builder $query) => $query
                                    ->where('company_id', $this->ownerRecord->company_id)
                                    ->where('purchase_currency_id', $this->ownerRecord->currency_id)
                            )
                            ->label('Producto')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {

                                $productId = $get('product_id');
                                if ($productId) {
                                    $product = Product::find($productId);
                                    if ($product && $product->purchase_price) {

                                        $set('unit_price', number_format($product->purchase_price, 4, '.', ''));
                                    } else {
                                        $set('unit_price', number_format(0, 4, '.', '')); // Resetear si no hay precio de compra
                                    }
                                } else {
                                    $set('unit_price', number_format(0, 4, '.', '')); // Resetear si no hay producto
                                }
                                // Recalcular siempre después de actualizar el estado del producto
                                self::recalculateItemAmounts($get, $set);
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required()
                            ->minValue(0.0001)
                            ->default(1)
                            ->live() // Es importante que sea live para los cálculos
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::recalculateItemAmounts($get, $set);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0.00)
                            ->prefix(fn() => $this->ownerRecord->currency?->symbol ?? '$')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::recalculateItemAmounts($get, $set);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('igv_percentage')
                            ->label('IGV %')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(18.00)
                            ->suffix('%')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::recalculateItemAmounts($get, $set);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal Ítem')
                            ->numeric()
                            ->readOnly()
                            ->default(0.00)
                            ->prefix(fn() => $this->ownerRecord->currency?->symbol ?? '$')
                            ->columnSpan(1)
                            ->extraInputAttributes(['class' => 'font-bold']),

                        Forms\Components\TextInput::make('igv_tax_amount')
                            ->label('Monto IGV Ítem')
                            ->numeric()
                            ->readOnly()
                            ->default(0.00)
                            ->prefix(fn() => $this->ownerRecord->currency?->symbol ?? '$')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total')
                            ->label('Total Ítem')
                            ->numeric()
                            ->readOnly()
                            ->default(0.00)
                            ->prefix(fn() => $this->ownerRecord->currency?->symbol ?? '$')
                            ->columnSpan(1)
                            ->extraInputAttributes(['class' => 'font-bold text-lg text-primary-600']),

                        Forms\Components\RichEditor::make('notes')
                            ->label('Notas del Ítem')
                            ->maxLength(65535)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric(decimalPlaces: 4, thousandsSeparator: ',')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio Unitario')
                    ->numeric(decimalPlaces: 4, thousandsSeparator: ',')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->numeric(decimalPlaces: 2, thousandsSeparator: ',')
                    ->sortable()
                    ->prefix(fn ($record): string => $record->purchase->currency?->symbol ?? '$'),

                Tables\Columns\TextColumn::make('igv_percentage')
                    ->label('IGV %')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('igv_tax_amount')
                    ->label('Monto IGV')
                    ->numeric(decimalPlaces: 2, thousandsSeparator: ',')
                    ->sortable()
                    ->prefix(fn ($record): string => $record->purchase->currency?->symbol ?? '$'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total Ítem')
                    ->numeric(decimalPlaces: 2, thousandsSeparator: ',')
                    ->sortable()
                    ->prefix(fn ($record): string => $record->purchase->currency?->symbol ?? '$'),

                Tables\Columns\TextColumn::make('purchase.currency.name')
                    ->label('Moneda')
                    ->placeholder('N/A')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        $this->ownerRecord->recalculateTotals();
                        $this->dispatch('refreshPurchaseForm');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        $this->ownerRecord->recalculateTotals();
                        $this->dispatch('refreshPurchaseForm');
                        
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        $this->ownerRecord->recalculateTotals();
                        $this->dispatch('refreshPurchaseForm');
                        
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $this->ownerRecord->recalculateTotals();
                            $this->dispatch('refreshPurchaseForm');
                        }),
                ]),
            ]);
    }

    public static function recalculateItemAmounts(Get $get, Set $set): void
    {
        $quantity = (float) $get('quantity');
        $unitPrice = (float) $get('unit_price');
        $igvPercentage = (float) $get('igv_percentage');

        $subtotal = $quantity * $unitPrice;
        $calculatedIgv = ($subtotal * $igvPercentage) / 100;
        $total = $subtotal + $calculatedIgv;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('igv_tax_amount', number_format($calculatedIgv, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }


    protected function mutateTableQuery(Builder $query): Builder
    {
        return $query->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
