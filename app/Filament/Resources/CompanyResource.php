<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $modelPluralLabel = 'Empresas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('corporate_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre de la Empresa'),
                Forms\Components\TextInput::make('ruc')
                    ->required()
                    ->live()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'El RUC ya está en uso. Por favor, ingrese un RUC diferente.',
                    ])
                    ->maxLength(20)
                    ->label('RUC'),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->live()
                    ->label('Dirección'),
                PhoneInput::make('phone_number')
                    ->live()
                    ->inputNumberFormat(PhoneInputNumberType::NATIONAL)
                    ->label('Número de Teléfono'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->live()
                    ->placeholder('correo@ejemplo.com')
                    ->maxLength(255)
                    ->label('Correo Electrónico'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Estado de la Empresa')
                    ->live()
                    ->default(true)
                    ->inline(false)
                    ->helperText(fn (Get $get)=>$get('is_active') ? 'La empresa está ACTIVA.' : 'La empresa está INACTIVA.')
                    ->hiddenOn('create'),
                Forms\Components\FileUpload::make('logo_url')
                    ->label('Logo de la Empresa')
                    ->image()
                    ->previewable(true)
                    ->live()
                    ->disk('public')
                    ->directory('company_logos')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable()
                    ->sortable(),
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->disk('public')
                    ->toggleable()
                    ->size(50)
                    ->circular(),
                TextColumn::make('corporate_name')
                    ->label('Nombre de la Empresa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ruc')
                    ->label('RUC')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->toggleable(true,false),
                    TextColumn::make('phone_number')
                    ->label('Teléfono')
                    ->toggleable(true,false),
                TextColumn::make('is_active')
                    ->label('Estado')
                    ->toggleable()
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn($state) => $state ? 'Activo' : 'Inactivo')
                    ->sortable(),

            ])
            ->filters([
                //
                SelectFilter::make('is_active')
                    ->options([
                        true => 'Activo',
                        false => 'Inactivo',
                    ])
                    ->label('Estado de la Empresa'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label('')
                        ->modalHeading('Eliminar Empresa')
                        ->modalDescription('Advertencia: Esta acción eliminará los datos de la empresa, no se podrá deshacer ni crear una nueva empresa con el mismo número de RUC. ¿Está seguro de continuar con la eliminación?'),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
            'view' => Pages\ViewCompany::route('/{record}'),
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
