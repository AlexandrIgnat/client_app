<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\HeaderActionsTrait;

class EditClient extends EditRecord
{
    use HeaderActionsTrait;
    protected static string $resource = ClientResource::class;
    protected static ?string $title = 'Редактирование клиента';

    protected function getHeaderActions(): array
    {
        return $this->getHeaderActionsTrait();
    }
}
