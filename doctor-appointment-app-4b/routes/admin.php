<?php //decir que es un archivo php
//es una importacion de la clase Route del framework Laravel- Route configuara las rutas de la aplicacion
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

//gestion de roles
Route::resource('roles', RoleController::class);