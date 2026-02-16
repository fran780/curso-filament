<?php

namespace App\Filament\Personal\Resources\Timesheets\Pages;

use App\Filament\Personal\Resources\Timesheets\TimesheetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
