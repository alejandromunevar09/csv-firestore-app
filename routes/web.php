<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeopleController;

Route::get('/', fn () => redirect()->route('people.index'));

// CRUD
Route::resource('people', PeopleController::class)->parameters(['people' => 'id']);

// Búsqueda
Route::get('people-search', [PeopleController::class, 'search'])->name('people.search');

// Importar CSV (form + proceso)
Route::get('people-import', [PeopleController::class, 'importForm'])->name('people.import.form');
Route::post('people-import', [PeopleController::class, 'import'])->name('people.import.process');