<?php

namespace App\Filament\Resources\Users\Schemas;

use Altwaireb\World\Models\State;
use Altwaireb\World\Models\City;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section; // para incluir una seccion y dividir mejor el contenido
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Collection;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Info') //seccion donde dentro de ella van los tipos de campos
                    ->columns(3) //esto sirve para colocar en cuantos numeros de columnas quiero ubicar los label
                    ->schema([
                        TextInput::make('name')  // campo para escribir
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->hiddenOn('edit') //sirve para hacer que el campo contraseña este oculto en editar
                            ->required(),
                    ]),

                Section::make('Address Info')
                    ->columns(3)
                    ->schema([
                        Select::make('country_id') //campo de seleccion
                            ->relationship(name: 'country', titleAttribute: 'name') //sirve para enlazar un registro con un país relacionado y mostrar el nombre de ese país en la interfaz de usuario.
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            })
                            ->required(),
                        Select::make('state_id')
                            ->options(fn(Get $get): Collection => State::query() // sirva para para definir opciones dinámicas para un Selectcampo de formulario
                                ->where('country_id', $get('country_id')) // filtrar dinámicamente registros 
                                ->pluck('name', 'id')) //extrer estos valores especificos de la columna
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('city_id', null)) //despues de borrar debe borrar tambien el de city
                            ->required(),
                        Select::make('city_id')
                            ->options(fn(Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        TextInput::make('address')
                            ->required(),
                        TextInput::make('postal_code')
                            ->required(),
                    ]),
            ]);
    }
}
