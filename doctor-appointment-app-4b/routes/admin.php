<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de roles
Route::resource('roles', RoleController::class)->names('roles');
Route::resource('users', UserController::class)->names('users');
Route::get('patients/import', [PatientController::class, 'importForm'])->name('patients.import');
Route::post('patients/import', [PatientController::class, 'importStore'])->name('patients.import.store');
Route::get('patients/import/progress', [PatientController::class, 'importProgress'])->name('patients.import.progress');
Route::delete('patients/all', [PatientController::class, 'destroyAll'])->name('patients.destroy-all');
Route::resource('patients', PatientController::class)->names('patients');
Route::resource('doctors', DoctorController::class)->names('doctors');

// Gestión de citas
Route::resource('appointments', AppointmentController::class)->names('appointments');
Route::get('appointments/{appointment}/consultation', [AppointmentController::class, 'consultation'])->name('appointments.consultation');
