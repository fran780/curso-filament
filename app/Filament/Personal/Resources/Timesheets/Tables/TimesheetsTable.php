<?php

namespace App\Filament\Personal\Resources\Timesheets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TimesheetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('calendar.name') //esto sirve para los campos donde tengo relacion puedo mostrar en vez del id, el nombre ya que ambas cosas estan relacionadas
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('day_in')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('day_out')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type') //filtrar
                    ->options([
                        'work' => 'Working',
                        'pause' => 'In Pause',
                    ]),

            ])
            ->recordActions([ //esto es para los botones de editar y borrar
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    //Con esto puedo exportar forms, tablas aun no funcionan
                    ExportBulkAction::make()->exports([
                        ExcelExport::make('form')->fromForm()
                            ->withFilename('Timesheet_' . date('Y-m-d') . ' _export'),
                        /*->withColumns([
                                Column::make('User'),
                                Column::make('created_at'),
                                Column::make('deleted_at'),
                            ]),*/
                        ExcelExport::make('form2')->fromForm()
                            //permite seleccionar el tipo de archivo y el nombre de como se quiere exportar
                            ->askForFilename()
                            ->askForWriterType(),
                    ])
                ])
            ]);
    }
}