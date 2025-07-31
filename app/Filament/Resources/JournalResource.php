<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalResource\Pages;
use App\Filament\Resources\JournalResource\RelationManagers;
use App\JournalType;
use App\Models\Journal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Diario';
    protected static ?string $pluralModelLabel = 'Diarios';
    protected static ?string $navigationGroup = 'Configuración';

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
                            ->disabledOn('edit') // La empresa no se cambia después de crear
                            ->dehydrated(fn (?string $state) => filled($state)) // Asegura que se guarde en create
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Diario')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), // Nombre único por diario

                        Forms\Components\Select::make('type')
                            ->label('Tipo de Diario')
                            ->enum(JournalType::class) // Usa el Enum para las opciones
                            ->options(JournalType::class)
                            ->required()
                            ->live() // Para filtrar los tipos de documento si es necesario
                            ->helperText('Define la naturaleza de las transacciones de este diario (Ventas, Compras, Caja, etc.)'),

                        Forms\Components\Select::make('document_type_id')
                            ->relationship(
                                'documentType',
                                'name',
                                // Filtra los tipos de documento según el tipo de diario seleccionado
                                fn (Builder $query, Get $get) => match ($get('type')) {
                                    JournalType::SALES->value => $query->whereIn('applicable_to', ['sales', 'both']),
                                    JournalType::PURCHASE->value => $query->whereIn('applicable_to', ['purchase', 'both']),
                                    default => $query->where('applicable_to', 'other')->orWhereNull('applicable_to') // O sin filtro si es otro tipo
                                }
                            )
                            ->label('Tipo de Documento Asociado')
                            ->placeholder('Selecciona un tipo de documento')
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->helperText('Tipo de comprobante que se generará (Factura, Boleta, Nota de Crédito, etc.)'),

                        Forms\Components\TextInput::make('series_prefix')
                            ->label('Prefijo de Serie')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, Get $get) {
                                // Asegura que la serie sea única por empresa y tipo de documento (si aplica)
                                return $rule->where('company_id', $get('company_id'))
                                            ->where('document_type_id', $get('document_type_id'));
                            })
                            ->helperText('Ej: F001 para Facturas, B001 para Boletas. Debe ser único para esta empresa y tipo de documento.'),

                        Forms\Components\TextInput::make('current_number')
                            ->label('Número Correlativo Actual')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(0)
                            ->helperText('El último número utilizado en esta serie. Se incrementará automáticamente.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->required()
                            ->default(true)
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Diario')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (JournalType $state): string => $state->getLabel())
                    ->color(fn (JournalType $state): string|array|null => $state->getColor())
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Tipo Documento')
                    ->placeholder('N/A')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('series_prefix')
                    ->label('Serie')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_number')
                    ->label('Número Actual')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
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
                Tables\Filters\SelectFilter::make('type')
                    ->label('Filtrar por Tipo de Diario')
                    ->options(JournalType::class),
                Tables\Filters\SelectFilter::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->label('Filtrar por Tipo de Documento')
                    ->preload()
                    ->searchable()
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
            'index' => Pages\ListJournals::route('/'),
            'create' => Pages\CreateJournal::route('/create'),
            'edit' => Pages\EditJournal::route('/{record}/edit'),
            'view'=> Pages\ViewJournal::route('/{record}')
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
