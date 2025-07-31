<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Filament\Resources\DocumentTypeResource\RelationManagers;
use App\Models\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Tipo de Documento';
    protected static ?string $pluralModelLabel = 'Tipos de Documentos';
    protected static ?string $navigationGroup = 'Configuración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true), // Asegura nombres únicos

                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(5)
                    ->unique(ignoreRecord: true) // Asegura códigos únicos (ej. '01', '03')
                    ->helperText('Código oficial para el tipo de documento (ej. SUNAT, SRI, etc.)'),

                Forms\Components\TextInput::make('description')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\Select::make('applicable_to')
                    ->label('Aplicable a')
                    ->options([
                        'sales' => 'Ventas',
                        'purchase' => 'Compras',
                        'both' => 'Ambos (Ventas y Compras)',
                        'other' => 'Otro'
                    ])
                    ->default('both')
                    ->nullable()
                    ->helperText('Indica si este tipo de documento se usa para ventas, compras o ambos.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->wrap() // Permite que la descripción se ajuste si es larga
                    ->placeholder('Sin descripción'),

                Tables\Columns\TextColumn::make('applicable_to')
                    ->label('Aplicable a')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'sales' => 'Ventas',
                        'purchase' => 'Compras',
                        'both' => 'Ambos',
                        'other' => 'Otro',
                        default => $state,
                    })
                    ->badge()
                    ->colors([
                        'info' => 'both',
                        'success' => 'sales',
                        'warning' => 'purchase',
                        'gray' => 'other'
                    ])
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
                Tables\Filters\SelectFilter::make('applicable_to')
                    ->label('Filtrar por Aplicación')
                    ->options([
                        'sales' => 'Ventas',
                        'purchase' => 'Compras',
                        'both' => 'Ambos',
                        'other' => 'Otro',
                    ]),
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
            'view'=>Pages\ViewDocumentType::route('/{record}')
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
