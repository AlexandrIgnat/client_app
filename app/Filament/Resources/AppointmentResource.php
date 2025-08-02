<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use App\Enums\Month;
use Illuminate\Support\HtmlString;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Support\Str;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'Расписание';
    protected static ?string $navigationLabel = 'Расписание';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Клиент')
                    ->relationship('client', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Время')
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Стоимость')
                    ->numeric()
                    ->inputMode('decimal')
                    ->step(0.01)
                    ->prefix('₽')
                    ->rules(['numeric', 'min:0']),

                Forms\Components\Checkbox::make('open')
                    ->label('Отображать для записи')
                    ->default(false)
                    ->extraAttributes(['class' => 'flex items-center']),


                Forms\Components\Textarea::make('comments')
                    ->label('Комментарий')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                    Tables\Columns\TextColumn::make('start_time')
                        ->label('Время')
                        ->dateTime('H:i')
                        ->weight('medium')
                        ->color('primary'),

                    Tables\Columns\TextColumn::make('client.name')
                        ->label('Клиент')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('payment_status') // Виртуальное поле
                        ->label('Статус оплаты')
                        ->badge()
                        ->state(function ($record) {
                            return !is_null($record->price) ? 'Оплачено: ' . number_format($record->price, 2) . ' ₽' : 'Не оплачено';
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'Не оплачено' => 'danger',
                            default => 'success',
                        })
                        ->html(),

                    Tables\Columns\TextColumn::make('comments')
                        ->label('Комментарий')
                        ->limit(30)
                        ->toggleable(isToggledHiddenByDefault: true),
                ])

            ->groups([
                Group::make('start_time')
                    ->label('День недели')
                    ->getTitleFromRecordUsing(function ($record) {
                        return $record->start_time->translatedFormat('l, j F Y');
                    })
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->orderQueryUsing(
                    fn (Builder $query, string $direction) => $query->orderBy('start_time', 'desc') // Жестко задаем сортировку
                ),
            ])
            ->defaultGroup('start_time')
            // ->defaultSort('start_time', 'asc') // Жестко задаем сортировку
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->label('Месяц')
                    ->options(function () {
                        $months = [];
                        $current = Carbon::now();

                        for ($i = 0; $i < 12; $i++) {
                            $date = $current->copy()->subMonths($i);
                            $m = Month::from($date->translatedFormat('m'));
                            $months[$date->format('Y-m')] = sprintf('%s %s', $m->label(), $date->translatedFormat('Y'));
                        }

                        return $months;
                    })
                    // ->default(Carbon::now()->format('Y-m'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $date = Carbon::createFromFormat('Y-m', $data['value']);
                            return $query
                                ->whereYear('start_time', $date->year)
                                ->whereMonth('start_time', $date->month);
                        }
                        return $query;
                    }),
                Tables\Filters\Filter::make('clients_filter')
                    ->form([
                        Select::make('client_ids')
                            ->label('Клиенты')
                            ->multiple()
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['client_ids'])) {
                            $query->whereIn('client_id', $data['client_ids']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
