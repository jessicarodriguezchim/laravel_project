<?php //decir que es un archivo php
//es una importacion de la clase Route del framework Laravel- Route configuara las rutas de la aplicacion
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "welcome to my doctor appointment app";
});
