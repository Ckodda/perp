<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Currency;
use App\Models\Purchase;
use App\PurchaseStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Compras y Proveedores';

    protected static ?string $modelLabel = 'Compra';
    protected static ?string $pluralModelLabel = 'Compras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'corporate_name')
                            ->label('Empresa')
                            ->required()
                            ->default(fn () => Auth::user()->company_id)
                            ->disabledOn('edit')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->searchable()
                            ->preload()
                            ->live(),

                        Forms\Components\Select::make('supplier_id')
                            ->relationship(
                                'supplier',
                                'name',
                                fn (Builder $query, Forms\Get $get) => $query->where('company_id', $get('company_id'))
                            )
                            ->label('Proveedor')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Fecha de Compra')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Número de Factura')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->nullable(),

                        Forms\Components\Select::make('currency_id')
                            ->relationship('currency', 'name')
                            ->label('Moneda')
                            ->required()
                            ->searchable()
                            ->live()
                            ->preload(),


                        Forms\Components\TextInput::make('subtotal_amount')
                            ->label('Subtotal (Sin IGV)')
                            ->numeric()
                            ->required()
                            ->default(0.00)
                            ->prefix(fn (Get $get)=> Currency::find($get('currency_id'))->symbol?? '$')
                            ->disabled()
                            ->extraAttributes(['class' => 'font-bold text-lg']),
                        Forms\Components\TextInput::make('igv_percentage')
                            ->label('Porcentaje de IGV')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(18.00)
                            ->suffix('%')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $subtotal = (float) $get('subtotal_amount');
                                $igvPercentage = (float) $get('igv_percentage');

                                $calculatedIgv = ($subtotal * $igvPercentage) / 100;
                                $calculatedTotal = $subtotal + $calculatedIgv;

                                $set('igv_tax_amount', number_format($calculatedIgv, 2, '.', ''));
                                $set('total_amount', number_format($calculatedTotal, 2, '.', ''));
                            }),

                        Forms\Components\TextInput::make('igv_tax_amount')
                            ->label('Monto IGV')
                            ->numeric()
                            ->nullable()
                            ->default(null)
                            ->prefix(fn (Get $get)=> Currency::find($get('currency_id'))->symbol?? '$')
                            ->disabled()
                            ->live(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Monto Total')
                            ->numeric()
                            ->required()
                            ->default(0.00)
                            ->prefix(fn (Get $get)=> Currency::find($get('currency_id'))->symbol?? '$')
                            ->disabled()
                            ->afterStateHydrated(function (?Purchase $record, Forms\Components\TextInput $component) {
                                
                                if ($record) {
                                    $component->state($record->total_amount);
                                }
                            })
                            ->dehydrateStateUsing(function (Forms\Get $get) {
                                
                                $subtotal = (float) $get('subtotal_amount');
                                $igv = (float) $get('igv_tax_amount');
                                return $subtotal + $igv;
                            })
                            ->extraAttributes(['class' => 'font-bold text-xl text-primary-600']),

                        
                        Forms\Components\Select::make('status')
                            ->label('Estado de la Compra')
                            ->enum(PurchaseStatus::class)
                            ->options(PurchaseStatus::class)
                            ->required()
                            ->default(PurchaseStatus::PENDING->value)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('notes')
                            ->label('Notas')
                            ->maxLength(65535)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.corporate_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Fecha Compra')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('N° Factura')
                    ->searchable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('subtotal_amount') 
                    ->label('Subtotal')
                    ->numeric(decimalPlaces: 2, thousandsSeparator: ',')
                    ->sortable(),
                Tables\Columns\TextColumn::make('igv_percentage') // <--- ¡NUEVA COLUMNA!
                    ->label('IGV %')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('igv_tax_amount')
                    ->label('IGV')
                    ->numeric(decimalPlaces: 2, thousandsSeparator: ',')
                    ->placeholder('0.00')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Monto Total')
                    ->numeric(decimalPlaces: 2, thousandsSeparator: ',')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.name')
                    ->label('Moneda')
                    ->placeholder('N/A')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (PurchaseStatus $state): string => $state->getLabel())
                    ->color(fn (PurchaseStatus $state): string|array|null => $state->getColor())
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Registro')
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
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'corporate_name')
                    ->label('Filtrar por Empresa')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->relationship(
                        'supplier',
                        'name',
                        fn (Builder $query, Forms\Get $get) => $query->where('company_id', $get('company_id'))
                    )
                    ->label('Filtrar por Proveedor')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    ->options(PurchaseStatus::class),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PurchaseItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
            'view' => Pages\ViewPurchase::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
