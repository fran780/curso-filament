<?php

namespace App\Filament\Personal\Resources\Holidays;

use App\Filament\Personal\Resources\Holidays\Pages\CreateHoliday;
use App\Filament\Personal\Resources\Holidays\Pages\EditHoliday;
use App\Filament\Personal\Resources\Holidays\Pages\ListHolidays;
use App\Filament\Personal\Resources\Holidays\Schemas\HolidayForm;
use App\Filament\Personal\Resources\Holidays\Tables\HolidaysTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Models\Holiday;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    // sirve para filtrar los registros de un recurso por el usuario autenticado actualmente. 
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id);
    }

    // sirve para redirigir a la direccion indicada en el getUrl
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static ?string $recordTitleAttribute = 'Holiday';

    public static function form(Schema $schema): Schema
    {
        return HolidayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HolidaysTable::configure($table);
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
            'index' => ListHolidays::route('/'),
            'create' => CreateHoliday::route('/create'),
            'edit' => EditHoliday::route('/{record}/edit'),
        ];
    }
}
