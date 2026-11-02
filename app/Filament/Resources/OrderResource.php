<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Покупки';
    protected static ?string $modelLabel = 'Заказ';
    protected static ?string $pluralModelLabel = 'Заказы';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Заказ')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название')->required()->maxLength(255)
                    ->default(fn() => 'Заказ от ' . now()->format('d.m.Y')),
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->required()->searchable()->preload(),
                Forms\Components\Textarea::make('notes')
                    ->label('Заметки')->columnSpanFull(),
            ])->columns(2),

            // Итоги теперь берутся напрямую из модели
            Forms\Components\Section::make('Итого')->schema([
                Forms\Components\Placeholder::make('upcoming_total')
                    ->label('🕒 Нужно на предстоящие покупки')
                    ->content(fn(?Order $record) => number_format($record?->upcoming_total ?? 0, 2, '.', ' ') . ' ₽'),
                Forms\Components\Placeholder::make('purchased_total')
                    ->label('✅ Уже куплено на')
                    ->content(fn(?Order $record) => number_format($record?->purchased_total ?? 0, 2, '.', ' ') . ' ₽'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Заказ')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Пользователь')
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')->label('Позиций')->sortable(),
                Tables\Columns\TextColumn::make('upcoming_total')
                    ->label('🕒 Купить на')->money('RUB'),
                Tables\Columns\TextColumn::make('purchased_total')
                    ->label('✅ Куплено на')->money('RUB'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')->dateTime('d.m.Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('copy')
                    ->label('Копировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        // Явно исключаем вычисляемый атрибут items_count
                        $copy = $record->replicate(['created_at', 'updated_at', 'items_count']);
                        $copy->name = $record->name . ' (копия)';
                        $copy->save();

                        foreach ($record->items as $item) {
                            $new = $item->replicate(['created_at', 'updated_at']);
                            $new->order_id = $copy->id;
                            $new->is_purchased = false;
                            $new->purchased_at = null;
                            $new->save();
                        }
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
