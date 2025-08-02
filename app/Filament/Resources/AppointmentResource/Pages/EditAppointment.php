<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\HeaderActionsTrait;

class EditAppointment extends EditRecord
{
    use HeaderActionsTrait;
    protected static string $resource = AppointmentResource::class;
    protected static ?string $title = 'Редактирование записи';

    protected function getHeaderActions(): array
    {
        return $this->getHeaderActionsTrait();
    }
}
