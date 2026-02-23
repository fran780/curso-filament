<?php

namespace App\Imports;

use App\Models\Calendar;
use App\Models\Timesheet;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class MyTimesheetImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable;

    public function __construct(private readonly int $userId) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {
            try {
                $calendarName = trim((string) ($row['calendario'] ?? ''));

                $calendar = Calendar::whereRaw('LOWER(name) = ?', [mb_strtolower($calendarName)])->first();

                if (! $calendar) {
                    throw new \Exception("Calendario no existe: '{$calendarName}'");
                }

                Timesheet::create([
                    'calendar_id' => $calendar->id,
                    'user_id'     => $this->userId,
                    'type'        => $row['tipo'],
                    'day_in'      => $this->parseDate($row['hora_de_entrada'] ?? null),
                    'day_out'     => $this->parseDate($row['hora_de_salida'] ?? null),
                ]);
            } catch (\Throwable $e) {
                throw new \Exception("Error importando fila " . ($i + 2) . ": " . $e->getMessage(), 0, $e);
            }
        }
    }

    public function rules(): array
    {
        return [
            '*.calendario'      => ['required', 'string'],
            '*.tipo'            => ['required', Rule::in(['work', 'pause'])],
            '*.hora_de_entrada' => ['nullable', 'date'],
            '*.hora_de_salida'  => ['nullable', 'date'],
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        // En este momento esta vacío pero puedo loguear $failures para revisar los errores de validación
    }

    private function parseDate(mixed $value): ?CarbonInterface
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
        }

        return Carbon::parse($value);
    }
}
