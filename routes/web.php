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

Auth::routes();

Route::get('/ci', 'Puton');

Route::group(['prefix' => 'admin'], function () {

    Voyager::routes();
    Route::get('parser', 'Parser');
    
});

Route::match(['get', 'post'], '/unrecognized', 'UnrecognizedController');

Route::match(['get', 'post'], '/windows', 'WindowsController@index');

Route::get('/home', 'HomeController@index')->name('home');
