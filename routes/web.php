<?php

use Illuminate\Support\Facades\Route;

//En esta parte de aqui sirve para poder redirigir a la ruta que yo desee
Route::get('/', function () {
    return redirect('/personal');
});
