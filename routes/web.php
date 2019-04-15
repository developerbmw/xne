<?php

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

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function() {
    Route::get('/', 'DashboardController@index');
    Route::resource('accounts', 'AccountsController');
    Route::resource('transactions', 'TransactionsController');

    Route::get('profile', 'ProfileController@index')->name('profile.index');
    Route::put('profile', 'ProfileController@update')->name('profile.update');

    Route::get('import', 'ImportController@showImportForm')->name('import.form');
    Route::post('import', 'ImportController@runImport')->name('import.run');

    Route::get('reports', 'ReportsController@index')->name('reports.index');
    Route::get('reports/run', 'ReportsController@run')->name('reports.run');
});
