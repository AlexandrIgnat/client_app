@props([
    'column' => null,
    'record' => null,
])

<div {{ $attributes->merge(['class' => 'filament-table-group-header']) }}>
    {{ $column->getTitle($record) }}
</div>
