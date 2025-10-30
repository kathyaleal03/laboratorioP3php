<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Estadísticas (definir antes del resource para evitar que el parámetro {empleado} capture 'estadisticas')
Route::get('empleados/estadisticas', [EmpleadoController::class, 'statistics'])->name('empleados.statistics');
// CRUD de empleados
Route::resource('empleados', EmpleadoController::class);
