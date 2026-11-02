<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Список покупок';
    protected static ?string $modelLabel = 'товар';
    protected static ?string $pluralModelLabel = 'товары';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('Товар')
                ->relationship('product', 'name')
                ->getOptionLabelFromRecordUsing(fn(Product $r) =>
                $r->name . ($r->brand ? " ({$r->brand})" : ''))
                ->searchable()->preload()->required()
                ->live()
                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    if ($p = Product::find($state)) {
                        $set('unit', $p->unit);
                        if (! $get('price')) {
                            $set('price', $p->default_price);
                        }
                    }
                })
                ->columnSpanFull(),

            Forms\Components\TextInput::make('quantity')
                ->label('Количество')->numeric()->default(1)->required(),

            Forms\Components\TextInput::make('unit')
                ->label('Ед. изм.')->maxLength(16),

            Forms\Components\TextInput::make('price')
                ->label('Фактическая цена')->numeric()->prefix('₽')
                ->helperText('Если пусто, возьмётся цена из товара'),

            Forms\Components\TextInput::make('comment')
                ->label('Комментарий (объём, вес)')
                ->placeholder('Например: 2 л, обезжиренное')
                ->columnSpanFull(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            // Сортируем: сначала те, что нужно купить, потом купленные
            ->modifyQueryUsing(fn($query) => $query->orderBy('is_purchased', 'asc')->orderBy('created_at', 'asc'))
            ->columns([
                // Переключатель статуса прямо в таблице!
                Tables\Columns\ToggleColumn::make('is_purchased')
                    ->label('Куплено')
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'is_purchased' => $state,
                            'purchased_at' => $state ? now() : null,
                        ]);
                    }),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Товар')
                    ->description(fn($record) => $record->product->brand)
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Кол-во'),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Ед.'),
                Tables\Columns\TextColumn::make('effective_price')
                    ->label('Цена')
                    ->money('RUB'),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Комментарий')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Дата покупки')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(),
            ])
            ->headerActions([
                // Кнопка добавления. Сохраняет СРАЗУ в модалке.
                Tables\Actions\CreateAction::make()
                    ->label('Добавить товар')
                    ->modalHeading('Новый товар в заказе')
                    ->modalWidth('xl')
                    ->after(function ($record) {
                        // Опционально: можно добавить нотификацию
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Редактировать товар')
                    ->modalWidth('xl'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
