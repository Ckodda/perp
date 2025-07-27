<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Filament\Resources\StockMovementResource\RelationManagers;
use App\Models\StockMovement;
use App\StockMovementType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationGroup = 'Inventario y Almacenes';

    protected static ?string $modelLabel = 'Movimiento';

    protected static ?string $pluralModelLabel = 'Movimientos';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->searchable()
                            ->preload()
                            ->disabledOn('edit'),

                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Producto')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),

                        Forms\Components\Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->label('Almacén')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('type')
                            ->label('Tipo de Movimiento')
                            ->options(StockMovementType::class)
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(1),

                        Forms\Components\TextInput::make('reference')
                            ->label('Referencia (N° Doc.)')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\RichEditor::make('notes')
                            ->label('Notas / Observaciones')
                            ->maxLength(65535)
                            ->nullable()
                            ->columnSpanFull(),


                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Usuario Registrador')
                            ->disabled()
                            ->default(fn() => Auth::user()->id)
                            ->dehydrated(fn(?string $state) => filled($state)),
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

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->placeholder('N/A') // Mostrar N/A si no hay almacén
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de Movimiento')
                    ->badge()
                    ->formatStateUsing(fn(StockMovementType $state): string => $state->getLabel()) // Usa el método getLabel del Enum
                    ->color(fn(StockMovementType $state): string|array|null => $state->getColor()) // Usa el método getColor del Enum
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Referencia')
                    ->searchable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->placeholder('Sistema/Desconocido')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Movimiento')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false), // Mostrar por defecto

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

                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Filtrar por Producto')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('Filtrar por Almacén')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Filtrar por Tipo')
                    ->options(StockMovementType::class), // Filtra por el Enum

                Tables\Filters\TrashedFilter::make(), // Para SoftDeletes
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
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
