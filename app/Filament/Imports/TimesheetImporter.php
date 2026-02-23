<?php

namespace App\Filament\Imports;

use App\Models\Timesheet;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TimesheetImporter extends Importer
{
    protected static ?string $model = Timesheet::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('calendar_id')
                ->requiredMapping()
                ->guess(['calendar_id', 'calendario_id'])
                ->numeric()
                ->rules(['required', 'integer', 'exists:calendars,id']), //
            ImportColumn::make('user_id')
                ->requiredMapping()
                ->guess(['user_id', 'usuario_id'])
                ->numeric()
                ->rules(['required', 'integer', 'exists:users,id']),
            ImportColumn::make('type')
                ->requiredMapping() 
                ->guess(['tipo', 'type'])
                ->rules(['required', 'in:work,pause']),

            ImportColumn::make('day_in')
                ->requiredMapping()
                ->guess(['hora_de_entrada', 'day_in'])
                ->castStateUsing(fn(mixed $state): ?string => self::normalizeDate($state))
                ->rules(['required', 'date']),

            ImportColumn::make('day_out')
                ->guess(['hora_de_salida', 'day_out']) //este guess sirve para que el sistema intente adivinar la columna correcta en base a los nombres asignados
                ->castStateUsing(fn(mixed $state): ?string => self::normalizeDate($state)) //este castStateUsing sirve para convertir el valor de la celda a un formato de fecha reconocido por Laravel, utilizando la función normalizeDate que se define más adelante
                ->rules(['nullable', 'date']), 
        ];
    }

    public function resolveRecord(): Timesheet
    {
        return new Timesheet();
    }

    protected static function normalizeDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Serial de Excel
        if (is_numeric($value)) {
            return Carbon::instance(Date::excelToDateTimeObject((float) $value))->toDateTimeString();
        }

        return Carbon::parse((string) $value)->toDateTimeString();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your timesheet import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}