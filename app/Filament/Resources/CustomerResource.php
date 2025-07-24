<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CustomerExporter;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // O un icono más adecuado
    protected static ?string $navigationGroup = 'Ventas'; // Agrupar con cosas de ventas
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_type')
                    ->options([
                        'DNI' => 'DNI',
                        'RUC' => 'RUC',
                        'Pasaporte' => 'Pasaporte',
                        'Otro' => 'Otro',
                    ])
                    ->required()
                    ->label('Tipo de Documento'),
                Forms\Components\TextInput::make('document_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Número de Documento')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre/Razón Social')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Apellido Paterno')
                    ->maxLength(255),
                Forms\Components\TextInput::make('maternal_last_name')
                    ->label('Apellido Materno')
                    ->maxLength(255),
                Forms\Components\TextInput::make('commercial_name')
                    ->label('Nombre Comercial')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),
                PhoneInput::make('phone_number')
                    ->live()
                    ->inputNumberFormat(PhoneInputNumberType::NATIONAL)
                    ->label('Número de Teléfono'),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('Ciudad')
                    ->maxLength(255),
                Forms\Components\TextInput::make('country')
                    ->label('País')
                    ->maxLength(255),
                Forms\Components\TextInput::make('economic_activity')
                    ->label('Actividad Económica')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Estado de la Empresa')
                    ->live()
                    ->default(true)
                    ->inline(false)
                    ->helperText(fn(Get $get) => $get('is_active') ? 'La empresa está ACTIVA.' : 'La empresa está INACTIVA.')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Tipo de Documento')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('document_number')
                    ->label('Número de Documento')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre/Razón Social')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido Paterno')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('maternal_last_name')
                    ->label('Apellido Materno')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('commercial_name')
                    ->label('Nombre Comercial')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Número de Teléfono')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('economic_activity')
                    ->label('Actividad Económica')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Estado')
                    ->toggleable()
                    ->badge()
                    ->color(fn(string $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn(string $state): string => $state ? 'Activo' : 'Inactivo'),
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
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar Productos')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(CustomerExporter::class)
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
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
