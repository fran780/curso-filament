<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PersonalWidgetStats extends StatsOverviewWidget
{
    // aqui mando a llamar los datos para mostrarlos tipo estadistica segun el tipo de usuario que este en este momento logueado
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Holidays', $this->getPendingHoliday(Auth::user())),
            Stat::make('Approved Holidays', $this->getApprovedHoliday(Auth::user())),
            Stat::make('Total Work', $this->getTotalWork(Auth::user())),
            Stat::make('Total Pause', $this->getTotalPause(Auth::user())),
        ];
    }

    // en esta parte hay dos funciones donde realiza un conteo de los datos que yo deseo donde los mando a llamar en la función de arriba igualmente según el usuario
    protected function getPendingHoliday(User $user)
    {
        $totalPendingHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'pending')->get()->count();

        return $totalPendingHolidays;
    }

    protected function getApprovedHoliday(User $user)
    {
        $totalApprovedHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'approved')->get()->count();

        return $totalApprovedHolidays;
    }

    protected function getTotalWork(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')->get();
        $sumSeconds = 0;
        foreach ($timesheets as $timesheet) {

            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumSeconds = $sumSeconds - $totalDuration;
        }
        //el gmdate junto con el H:i:s sirve para brindar la hora actual 
        $tiempoFormato = gmdate("H:i:s", $sumSeconds);
        return $tiempoFormato;
    }

    protected function getTotalPause(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'pause')->get();
        $sumSeconds = 0;
        foreach ($timesheets as $timesheet) {

            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumSeconds = $sumSeconds - $totalDuration;
        }
        //el gmdate junto con el H:i:s sirve para brindar la hora actual 
        $tiempoFormato = gmdate("H:i:s", $sumSeconds);
        return $tiempoFormato;
    }

}