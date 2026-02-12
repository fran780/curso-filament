<?php

namespace App\Filament\Resources\Departaments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;

class DepartamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                  TextInput::make('name')
                  ->required(),
            ]);
    }
}
