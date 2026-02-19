<?php

namespace App\Filament\Imports;

use App\Models\Timesheet;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class TimesheetImporter extends Importer
{
    protected static ?string $model = Timesheet::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('calendar_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('user_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('day_in')
                ->rules(['datetime']),
            ImportColumn::make('day_out')
                ->rules(['datetime']),
        ];
    }

    public function resolveRecord(): Timesheet
    {
        return new Timesheet();
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
