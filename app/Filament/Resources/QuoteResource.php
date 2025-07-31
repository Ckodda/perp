<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Currency;
use App\Models\Quote;
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

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'corporate_name')
                    ->label('Empresa vinculada')
                    ->live()
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('quote_date')
                    ->label('Fecha de Cotización')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('customer_id')
                    ->relationship(
                        'customer',
                        'name'
                    )
                    ->label('Cliente')
                    ->live()
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('currency_id')
                    ->relationship('currency', 'name')
                    ->label('Moneda')
                    ->live()
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('subtotal_amount')
                    ->label('Subtotal (Sin IGV)')
                    ->numeric()
                    ->required()
                    ->default(0.00)
                    ->prefix(fn(Get $get) => Currency::find($get('currency_id'))->symbol ?? '$')
                    ->live()
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
                    ->prefix(fn(Get $get) => Currency::find($get('currency_id'))->symbol ?? '$')
                    ->disabled()
                    ->live(),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Monto Total')
                    ->numeric()
                    ->required()
                    ->live()
                    ->default(0.00)
                    ->prefix(fn(Get $get) => Currency::find($get('currency_id'))->symbol ?? '$')
                    ->disabled()
                    ->afterStateHydrated(function (?Quote $record, Forms\Components\TextInput $component) {

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


                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Usuario Registrador')
                    ->disabled()
                    ->default(fn() => Auth::user()->id)
                    ->dehydrated(fn(?string $state) => filled($state)),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            RelationManagers\QuoteItemsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
