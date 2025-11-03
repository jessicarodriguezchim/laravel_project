<?php //decir que es un archivo php
//es una importacion de la clase Route del framework Laravel- Route configuara las rutas de la aplicacion
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;

Route::redirect('/', '/admin');
//Route::get('/', function () {
  //  return view('welcome');
//});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
});

