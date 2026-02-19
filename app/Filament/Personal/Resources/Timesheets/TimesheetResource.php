<?php

namespace App\Filament\Personal\Resources\Timesheets;

use App\Filament\Personal\Resources\Timesheets\Pages\CreateTimesheet;
use App\Filament\Personal\Resources\Timesheets\Pages\EditTimesheet;
use App\Filament\Personal\Resources\Timesheets\Pages\ListTimesheets;
use App\Filament\Personal\Resources\Timesheets\Schemas\TimesheetForm;
use App\Filament\Personal\Resources\Timesheets\Tables\TimesheetsTable;
use App\Models\Timesheet;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::TableCells;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id)->orderBy('day_in', 'desc');
    }

    protected static ?string $recordTitleAttribute = 'Timesheet';

    public static function form(Schema $schema): Schema
    {
        return TimesheetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimesheetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTimesheets::route('/'),
            'create' => CreateTimesheet::route('/create'),
            'edit' => EditTimesheet::route('/{record}/edit'),
        ];
    }
}
