<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

//En esta parte de aqui sirve para poder redirigir a la ruta que yo desee
Route::get('/', function () {
    return redirect('/personal');
});

//aqui es donde se maada el pdf segun el usuario, luego sigue la logica para descargar el pdf
Route::get('/pdf/generate/timesheet/{user}',[PdfController::class, 'TimesheetRecords'])->name('pdf.example');

//Ruta para up del docker
Route::get('/up', fn () => response('ok', 200));
