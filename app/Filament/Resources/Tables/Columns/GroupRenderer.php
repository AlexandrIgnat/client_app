<?php
namespace App\Filament\Resources\Tables\Columns;

use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;

class GroupRenderer extends Column
{
    protected string $view = 'filament.tables.columns.group-header';

    public function getTitle(): string
    {
        $date = $this->getRecord()->start_time;
        return $date->translatedFormat('l, d F Y'); // "Понедельник, 19 февраля 2024"
    }
}
