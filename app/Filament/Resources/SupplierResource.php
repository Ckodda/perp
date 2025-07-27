<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use App\TaxIdType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationGroup = 'Compras y Proveedores';

    protected static ?string $modelLabel = 'Proveedor';

    protected static ?string $pluralModelLabel = 'Proveedores';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        // Campos para el tipo de identificador y el número
                        Forms\Components\Select::make('tax_id_type')
                            ->label('Tipo de Identificación Fiscal')
                            ->enum(TaxIdType::class)
                            ->options(TaxIdType::class)
                            ->nullable()
                            ->live() 
                            ->columnSpan(1), 

                        Forms\Components\TextInput::make('tax_id')
                            ->label('Número de Identificación Fiscal')
                            ->maxLength(255)
                            ->nullable()
                            
                            ->unique(
                                'suppliers',
                                'tax_id', 
                                ignoreRecord: true,
                                modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, Forms\Get $get) {
                                    return $rule
                                        ->where('company_id', $get('company_id'))
                                        ->where('tax_id_type', $get('tax_id_type'));
                                }
                            )
                            ->columnSpan(1),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'corporate_name')
                            ->label('Empresa vinculada')
                            ->required()
                            ->default(fn() => Auth::user()->company_id)
                            ->disabledOn('edit')
                            ->dehydrated(fn(?string $state) => filled($state))
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Proveedor')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_person')
                            ->label('Persona de Contacto')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->nullable(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->nullable()
                            ->columnSpanFull(),

                         

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->hiddenOn('create')
                            ->live()
                            ->helperText(fn(string $state): string => $state ? 'El proveedor está activo.' : 'El proveedor está inactivo.')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.corporate_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Proveedor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contacto')
                    ->searchable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->placeholder('N/A'),

                // Columna para el tipo de identificador fiscal
                Tables\Columns\TextColumn::make('tax_id_type')
                    ->label('Tipo Documento Fiscal')
                    ->formatStateUsing(fn (?TaxIdType $state): ?string => $state?->getLabel()) // Usa el label del Enum
                    ->placeholder('N/A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_id')
                    ->label('Número Fiscal')
                    ->searchable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
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
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'corporate_name')
                    ->label('Filtrar por Empresa')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('tax_id_type')
                    ->label('Filtrar por Tipo de ID Fiscal')
                    ->options(TaxIdType::class), // Filtra por el Enum
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Activo')
                    ->falseLabel('Inactivo')
                    ->placeholder('Todos'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
            'view' => Pages\ViewSupplier::route('/{record}'),
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
