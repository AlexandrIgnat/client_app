<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\PriceHistoryRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Покупки';
    protected static ?string $modelLabel = 'Товар';
    protected static ?string $pluralModelLabel = 'Товары';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название')->required()->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->label('Марка')->maxLength(255),
                Forms\Components\TextInput::make('unit')
                    ->label('Ед. изм.')->placeholder('л, кг, шт'),
                Forms\Components\TextInput::make('default_price')
                    ->label('Цена по умолчанию')
                    ->numeric()->prefix('₽'),
                Forms\Components\Select::make('user_id')
                    ->label('Владелец (опционально)')
                    ->relationship('user', 'name')
                    ->searchable()->preload(),
                Forms\Components\Textarea::make('comment')
                    ->label('Комментарий')
                    ->placeholder('Например: 2 литра / 5 кг')
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Товар')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('brand')->label('Марка')
                    ->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('unit')->label('Ед.'),
                Tables\Columns\TextColumn::make('default_price')
                    ->label('Цена по умолч.')->money('RUB')->sortable(),
                Tables\Columns\TextColumn::make('last_price')
                    ->label('Последняя покупка')
                    ->getStateUsing(fn(Product $record) => $record->priceHistory()->first()?->price)
                    ->money('RUB'),
                Tables\Columns\TextColumn::make('user.name')->label('Владелец')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('Создан')
                    ->dateTime('d.m.Y')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('copy')
                    ->label('Копировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Product $record) {
                        // Оставляем только стандартные поля
                        $copy = $record->replicate(['created_at', 'updated_at']);
                        $copy->name = $record->name . ' (копия)';
                        $copy->save();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PriceHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
