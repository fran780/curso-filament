<?php

namespace App\Filament\Personal\Resources\Holidays\Pages;

use App\Filament\Personal\Resources\Holidays\HolidayResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    // Sirve para modificar los datos del formulario antes de guardarlos en la base de datos, lo cual ayuda cunado por ejemplo tengo campos que son obligatorios
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        $data['type'] = 'pending';
        return $data;
    }
}
