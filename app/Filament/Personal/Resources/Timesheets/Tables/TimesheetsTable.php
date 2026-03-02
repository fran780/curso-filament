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
                    //Con esto puedo exportar forms
                    ExportBulkAction::make()->exports([
                        ExcelExport::make('Form')
                            ->fromForm()
                            ->withFilename('timesheets_form_' . now()->format('Y-m-d_His')),
                        // Opción 2: exportar múltiples registros usando la tabla.
                        ExcelExport::make('Table')
                            ->withColumns([
                                Column::make('calendar.name')->heading('Calendar'),
                                Column::make('user.id')->heading('User'),
                                Column::make('type')->heading('Type'),
                                Column::make('day_in')->heading('Day in'),
                                Column::make('day_out')->heading('Day out'),
                                Column::make('created_at')->heading('Created at'),
                                Column::make('updated_at')->heading('Updated at'),
                        ])
                            ->withFilename('timesheets_table_' . now()->format('Y-m-d_His')),
                        ExcelExport::make('Form personalizado')
                            ->withColumns([
                                Column::make('calendar.name')->heading('Calendar'),
                                Column::make('user.id')->heading('User'),
                                Column::make('type')->heading('Type'),
                                Column::make('day_in')->heading('Day in'),
                                Column::make('day_out')->heading('Day out'),
                                Column::make('created_at')->heading('Created at'),
                                Column::make('updated_at')->heading('Updated at'),
                        ])
                            // Permite seleccionar el nombre del archivo y el formato de exportación.
                            ->askForFilename()
                            ->askForWriterType(),
                    ])
                ])
            ]);
    }
}