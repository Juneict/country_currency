<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('countries')->controller(CountryController::class)->group(function() {
        Route::get('/' , 'index')->name('countries.index');
        Route::get('/create' , 'create')->name('countires.create');
        Route::post('/' , 'store')->name('countries.store');
        Route::delete('/{id}', 'destroy')->name('countries.delete');
        Route::get('/{id}/edit', 'edit')->name('countires.edit');
        Route::put('/{id}' , 'update')->name('countries.update');
        Route::get('/{id}' , 'show')->name('countries.show');
    });

    Route::prefix('cities')->controller(CityController::class)->group(function() {
        Route::get('/create' , 'create')->name('cities.create');
        Route::post('/' , 'store')->name('cities.store');
        Route::delete('/{id}' , 'destroy')->name('cities.delete');
        Route::get('/{id}/edit', 'edit')->name('cities.edit');
        Route::put('/{id}' , 'update')->name('cities.update');
        Route::post('/fetch-from-api', 'fetchFromApi')->name('cities.fetch-from-api');
        Route::get('/{country_code}' , 'index')->name('cities.index');
    });
});
