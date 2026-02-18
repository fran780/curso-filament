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

    protected static ?string $navigationLabel = 'Vacaciones';

    // para que se vea el numero de cuantas solicitudes o lo que sea que se tenga de este archivo, en este caso son el numero de vacaciones
    public static function getNavigationBadge(): ?string
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id)->where('type', 'pending')->count();
    }

    //Sirve para hacer que mediante un rango en este caso de los tipos que estan pendientes pues se ponga del color indicado
    public static function getNavigationBadgeColor(): ?string
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id)->where('type', 'pending')->count() > 0 ? 'warning' : 'primary';
    }

    // sirve para filtrar los registros de un recurso por el usuario autenticado actualmente. 
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id);
    }

    // sirve para poner un mensaje al ponerse sobre el elemento indicado en este caso el numero de casos pendientes
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Numero de vacaciones pendientes';
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
