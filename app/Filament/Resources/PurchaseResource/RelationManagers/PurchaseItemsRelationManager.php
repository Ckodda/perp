<?php

namespace App\Filament\Resources\PurchaseResource\RelationManagers;

use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
                            ->relationship('product', 'name')
                            ->label('Producto')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->options(
                                fn (Forms\Get $get) => \App\Models\Product::where('company_id', $this->ownerRecord->company_id)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            ),
                            
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required()
                            ->minValue(0.0001)
                            ->default(1)
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0.00)
                            ->prefix(fn (Get $get)=> Currency::find($this->ownerRecord->currency_id)->symbol?? '$')
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal Ítem')
                            ->numeric()
                            ->readOnly()
                            ->default(0.00)
                            ->prefix(fn (Get $get)=> Currency::find($this->ownerRecord->currency_id)->symbol?? '$')
                            ->dehydrateStateUsing(function (Get $get) {
                                return (float) $get('quantity') * (float) $get('unit_price');
                            })
                            ->afterStateHydrated(function (?string $state, Get $get, Forms\Components\TextInput $component) {
                                
                                $quantity = (float) $get('quantity');
                                $unitPrice = (float) $get('unit_price');
                                $component->state(number_format($quantity * $unitPrice, 2, '.', ''));
                            })
                            ->columnSpanFull(),

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
                    ->numeric(
                        decimalPlaces: 0,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio Unitario')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->prefix(fn ($record)=>$record->purchase->currency?->symbol??'$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->prefix(fn ($record)=>$record->purchase->currency?->symbol??'$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal_igv')
                    ->label('Subtotal + IGV')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->prefix(fn ($record)=>$record->purchase->currency?->symbol??'$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.symbol')
                    ->label('Moneda')
                    ->default(fn ($record)=> "{$record->purchase->currency?->code} ({$record->purchase->currency->name})" ?? '$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        $this->ownerRecord->recalculateTotals();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        $this->ownerRecord->recalculateTotals();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        $this->ownerRecord->recalculateTotals();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $this->ownerRecord->recalculateTotals();
                        }),
                ]),
            ]);
    }

    // Asegúrate de que este método exista en tu modelo Purchase para recalcular los totales
    protected function mutateTableQuery(Builder $query): Builder
    {
        return $query->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
