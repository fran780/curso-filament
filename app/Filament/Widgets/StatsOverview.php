<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        //variables para que indicar la estadistica de cada uno
        $totalEmployees = User::all()->count();
        $totalHolidays = Holiday::where('type', 'pending')->count();
        $totalTimesheets = Timesheet::all()->count();

        return [
            //la variable stat sirve para mostrar el conteo o estadistica de mis datos, con las de description se muestra el total que hay mejor presentado, la de chart hace lo mismo
            Stat::make('Employees', $totalEmployees),
            Stat::make('Pending Holidays', $totalHolidays),
            Stat::make('Timesheets', $totalTimesheets),
                 //->description('32k increase')
                //->descriptionIcon('heroicon-m-arrow-trending-up'),
            //Una presentacion mejor
            /*Stat::make('Unique views', '192.1k')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),*/
        ];
    }
}
