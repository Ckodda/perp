<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del Producto')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'corporate_name')
                            ->label('Empresa')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nombre del Producto')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255),

                        Forms\Components\Select::make('unit_of_measure')
                            ->required()
                            ->label('Unidad de Medida (Kg)')
                            ->options([
                                'UNIT' => 'Unidad',
                                'LITER' => 'Litro',
                                'PC' => 'Paquete',
                            ])
                            ->default('UNIT'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->placeholder('Ingresar descripción del producto ...'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen del Producto')
                            ->disk('public')
                            ->directory('products')
                            ->image()
                            ->previewable(true)
                            ->live()
                            ->nullable()
                            ->uploadProgressIndicatorPosition('left')
                            ->maxSize(1024),

                            Forms\Components\Toggle::make('is_active')
                            ->label('Estado del Producto')
                            ->helperText(fn(string $state): string => $state ? 'El producto está activo.' : 'El producto está inactivo.')
                            ->hiddenOn('create')
                            ->required(),

                    ])->columns(2),
                Forms\Components\Section::make('Precio y Stock')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Precio de Compra')
                            ->numeric()
                            ->required()
                            ->default(0.00),
                        Forms\Components\Select::make('purchase_currency_id')
                            ->label('Moneda de Compra')
                            ->relationship('purchaseCurrency', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('sale_price')
                            ->label('Precio de Venta')
                            ->numeric()
                            ->required()
                            ->default(0.00),
                        Forms\Components\Select::make('sale_currency_id')
                            ->label('Moneda de Venta')
                            ->relationship('saleCurrency', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stock Actual')
                            ->numeric()
                            ->default(0.00)
                            ->readOnly()
                            ->helperText('El stock se actualiza automáticamente con los movimientos de inventario.'),
                        Forms\Components\TextInput::make('min_stock_alert')
                            ->label('Alerta de Stock Mínimo')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(2),
                Forms\Components\Section::make('Dimensiones y Peso')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label('Peso')
                            ->numeric()
                            ->live()
                            ->suffix(fn(Forms\Get $get) => $get('weight_unit'))
                            ->nullable(),
                        Forms\Components\Select::make('weight_unit')
                            ->label('Unidad de Peso')
                            ->options([
                                'TON' => 'Tonelada (TON)',
                                'KG' => 'Kilogramo (KG)',
                                'G' => 'Gramo (G)',
                            ])
                            ->live()
                            ->default('KG')
                            ->required(),

                        Forms\Components\TextInput::make('length')
                            ->label('Largo')
                            ->numeric()
                            ->suffix(fn(Forms\Get $get) => $get('dimension_unit'))
                            ->nullable(),
                        Forms\Components\TextInput::make('width')
                            ->label('Ancho')
                            ->numeric()
                            ->suffix(fn(Forms\Get $get) => $get('dimension_unit'))
                            ->nullable(),
                        Forms\Components\TextInput::make('height')
                            ->label('Alto')
                            ->numeric()
                            ->suffix(fn(Forms\Get $get) => $get('dimension_unit'))
                            ->nullable(),
                        Forms\Components\Select::make('dimension_unit')
                            ->label('Unidad de Dimensión')
                            ->options([
                                'M' => 'Metro (M)',
                                'CM' => 'Centímetro (CM)',
                                'MM' => 'Milímetro (MM)',
                            ])
                            ->default('CM')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_of_measure')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_stock_alert')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }
}
