<?php

namespace App\Filament\Personal\Resources\Holidays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('calendar_id')
                    ->relationship(name: 'calendar', titleAttribute: 'name')
                    ->required(),
                DatePicker::make('day')
                    ->required(),
            ]);
    }
}
