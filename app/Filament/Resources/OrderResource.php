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

    // МЕНЯЕМ НАЗВАНИЯ В UI (база данных не затрагивается)
    protected static ?string $modelLabel = 'Список';
    protected static ?string $pluralModelLabel = 'Списки покупок';
    protected static ?string $navigationLabel = 'Списки покупок';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Делаем секцию сворачиваемой и по умолчанию СВЕРНУТОЙ для экономии места
            Forms\Components\Section::make('Информация о списке')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Название списка')->required()->maxLength(255)
                        ->default(fn() => 'Список от ' . now()->format('d.m.Y')),
                    Forms\Components\Select::make('user_id')
                        ->label('Владелец')
                        ->relationship('user', 'name')
                        ->required()->searchable()->preload(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Заметки')->columnSpanFull(),
                ])->columns(2),

            // Компактная секция с итогами
            Forms\Components\Section::make('Итого по списку')
                ->collapsible()
                ->schema([
                    Forms\Components\Placeholder::make('upcoming_total')
                        ->label('🕒 Осталось купить на')
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
                Tables\Columns\TextColumn::make('name')->label('Название списка')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Владелец')
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')->label('Позиций')->sortable(),
                Tables\Columns\TextColumn::make('upcoming_total')
                    ->label('🕒 Купить на')->money('RUB'),
                Tables\Columns\TextColumn::make('purchased_total')
                    ->label('✅ Куплено на')->money('RUB')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')->dateTime('d.m.Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()->label('Открыть'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('copy')
                    ->label('Копировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
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
                    }),
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
