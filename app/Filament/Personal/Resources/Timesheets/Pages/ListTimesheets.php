<?php

namespace App\Filament\Personal\Resources\Timesheets\Pages;

use App\Filament\Personal\Resources\Timesheets\TimesheetResource;
use App\Models\Timesheet;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('inwork')
                ->label('Entrar a trabajar')
                ->color('success')
                ->requiresConfirmation() //sirve para mostrar una ventana emergente de confirmación
                ->action(function(){
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->day_out = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save(); //sirve para poder guardar 
                }),
            Action::make('inPause')
                ->label('Comenzar Pausa')
                ->color('info'),
            CreateAction::make(),
        ];
    }
}
