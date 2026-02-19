<?php

namespace App\Filament\Personal\Resources\Timesheets\Pages;

use App\Filament\Imports\TimesheetImporter;
use App\Filament\Personal\Resources\Timesheets\TimesheetResource;
use App\Imports\MyTimesheetImport;
use App\Models\Timesheet;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;
use App\Imports\UsersImport;
use App\Http\Controllers\Controller;
 use Barryvdh\DomPDF\Facade\Pdf;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first(); //con este metodo puedo colocar los timesheets en orden descendiente y tomando obviamente el primero 
        if ($lastTimesheet == null)
            return [
                Action::make('inwork')
                    ->label('Entrar a trabajar')
                    ->color('success')
                    ->requiresConfirmation() //sirve para mostrar una ventana emergente de confirmación
                    ->action(function () {
                        $user = Auth::user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->type = 'work';
                        $timesheet->save(); //sirve para poder guardar cuando tengo una ventana de confirmación 
                    }),
                CreateAction::make(),
            ];
        return [
            Action::make('inwork')
                ->label('Entrar a trabajar')
                ->keyBindings('alt+1', 'alt+1')
                ->color('success')
                ->visible(!$lastTimesheet->day_out == null) //areglar porque no funciona correctamente
                ->disabled($lastTimesheet->day_out == null)
                ->requiresConfirmation() //sirve para mostrar una ventana emergente de confirmación
                ->action(function () {
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save(); //sirve para poder guardar cuando tengo una ventana de confirmación 

                    Notification::make() //Notifiación al realizar la acción
                        ->title('Has entrado a trabajar')
                        ->body('Has comenzado a trabajar a las ' . Carbon::now())
                        ->color('success')
                        ->success()
                        ->send();
                    $this->dispatch('$refresh');
                }),
            Action::make('stopWork')
                ->label('Parar trabajo')
                ->keyBindings('alt+2', 'alt+2') // sirve para realizar la accion según la tecla indicada
                ->color('success')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
                ->disabled(!$lastTimesheet->day_out == null) // Arreglar
                ->requiresConfirmation() //sirve para mostrar una ventana emergente de confirmación
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    Notification::make() //Notifiación al realizar la acción
                        ->title('Has parado de trabajar')
                        ->color('success')
                        ->success()
                        ->send();
                    $this->dispatch('$refresh');
                }),
            Action::make('inPause')
                ->label('Comenzar Pausa')
                ->color('info')
                ->requiresConfirmation()
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause') // Arreglar
                ->disabled(!$lastTimesheet->day_out == null) // Arreglar
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'pause';
                    $timesheet->save();
                    Notification::make() //Notifiación al realizar la acción
                        ->title('Comienzas tu pausa')
                        ->color('info')
                        ->success()
                        ->send();
                    $this->dispatch('$refresh');
                }),
            Action::make('stopPause')
                ->label('Parar Pausa')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type == 'pause')
                ->disabled(!$lastTimesheet->day_out == null) //Arreglar
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                    Notification::make() //Notificación al realizar la acción
                        ->title('Comienzas de nuevo a trabajar')
                        ->color('info')
                        ->success()
                        ->send();
                    $this->dispatch('$refresh');
                }),
            CreateAction::make(),
            ImportAction::make()
                ->label("Import")
                ->importer(TimesheetImporter::class),
                Action::make('createPDF')
                ->label('Crear PDF')
                ->color('warning')
                ->requiresConfirmation()
                ->url( //ruta para exportar el PDF
                    fn (): string => route('pdf.example', ['user' => Auth::user()]),
                    shouldOpenInNewTab: true, // abrir en en una nueva ventana
                ),
        ];
    }
}

//Me queda pendiente arreglar lo de las zona horaria, además de los botones que no desaparecen cuando se toca uno.