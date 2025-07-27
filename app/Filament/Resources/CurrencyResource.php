<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Moneda';

    protected static ?string $pluralModelLabel = 'Monedas';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nombre de moneda')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->label('Código de moneda')
                    ->maxLength(3),
                Forms\Components\TextInput::make('symbol')
                    ->required()
                    ->label('Símbolo de moneda')
                    ->maxLength(10),
                Forms\Components\TextInput::make('decimal_separator')
                    ->required()
                    ->label('Separador decimal')
                    ->maxLength(1)
                    ->default('.'),
                Forms\Components\TextInput::make('thousands_separator')
                    ->required()
                    ->label('Separador de milésimas')
                    ->maxLength(1)
                    ->default(','),
                Forms\Components\TextInput::make('decimal_digits')
                    ->required()
                    ->numeric()
                    ->label('Cantidad de dígitos decimales')
                    ->default(2),
                Forms\Components\Toggle::make('symbol_first')
                ->label('¿Símbolo al inicio?')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Estado de la moneda')
                    ->live()
                    ->helperText(fn(string $state): string => $state ? 'Moneda ACTIVA' : 'Moneda INACTIVA')
                    ->hiddenOn('create')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->searchable(),
                Tables\Columns\TextColumn::make('decimal_separator')
                    ->searchable(),
                Tables\Columns\TextColumn::make('thousands_separator')
                    ->searchable(),
                Tables\Columns\TextColumn::make('decimal_digits')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('symbol_first')
                    ->boolean(),
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
            'view' => Pages\ViewCurrency::route('/{record}'),
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
