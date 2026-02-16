<?php

namespace App\Filament\Personal\Resources\Timesheets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TimesheetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('calendar_id')
                    ->relationship(name: 'calendar', titleAttribute: 'name')
                    ->required(),
                Select::make('type')
                    ->options([
                        'work' => 'Working',
                        'pause' => 'In Pause',
                    ])
                    ->required(),
                DateTimePicker::make('day_in')
                    ->required(),
                DateTimePicker::make('day_out')
                    ->required(),
            ]);
    }
}
