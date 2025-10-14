<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeopleController;

// Healthcheck (útil para verificar que el server responde)
Route::get('/health', fn () => 'ok')->name('health');

// Home → Listado
Route::get('/', fn () => redirect()->route('people.index'))->name('home');

// CRUD RESTful (usa {id} como parámetro)
Route::resource('people', PeopleController::class)->parameters(['people' => 'id']);

// Búsqueda
Route::get('people-search', [PeopleController::class, 'search'])->name('people.search');

// Importación CSV (formulario + proceso)
Route::get('people-import', [PeopleController::class, 'importForm'])->name('people.import.form');
Route::post('people-import', [PeopleController::class, 'import'])->name('people.import.process');
