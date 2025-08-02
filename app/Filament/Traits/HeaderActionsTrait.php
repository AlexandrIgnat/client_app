<?php

namespace App\Filament\Traits;

use Filament\Actions;

trait HeaderActionsTrait
{
    public function getHeaderActionsTrait(): array
    {
        return [
                    \Filament\Actions\Action::make('back')
                    ->label('Назад')
                    ->icon('heroicon-o-arrow-left')
                    ->url(static::getResource()::getUrl('index')),
                    Actions\DeleteAction::make(),
                ];
            }
}
