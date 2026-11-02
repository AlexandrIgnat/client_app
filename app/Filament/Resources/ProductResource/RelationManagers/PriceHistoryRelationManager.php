<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PriceHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';
    protected static ?string $title = 'История цен';
    protected static ?string $modelLabel = 'позиция';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('price')->where('is_purchased', true))
            ->defaultSort('purchased_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('purchased_at')->label('Дата')
                    ->dateTime('d.m.Y')->sortable(),
                Tables\Columns\TextColumn::make('order.name')->label('Заказ'),
                Tables\Columns\TextColumn::make('price')->label('Цена')
                    ->money('RUB')->sortable(),
                Tables\Columns\TextColumn::make('quantity')->label('Кол-во'),
                Tables\Columns\TextColumn::make('unit')->label('Ед.'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Сумма')
                    ->getStateUsing(fn($record) => $record->effective_total)
                    ->money('RUB'),
            ]);
    }
}
