<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class GroupHeaderColumn extends Column
{
    protected string $view = 'filament.tables.columns.group-header';

    public function getTitle(Model $record): HtmlString
    {
        return new HtmlString('
            <div class="flex items-center gap-2 bg-primary-50 px-4 py-2 text-primary-800 dark:bg-primary-900 dark:text-primary-100 rounded-lg">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium">'
                    . $record->start_time->translatedFormat('l, j F Y') .
                '</span>
            </div>
        ');
    }
}
