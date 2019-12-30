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

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('parser', 'Parser');
});


Route::group(['prefix' => '/manager', 
   'namespace' => 'Manager'
], function () {
    Route::get('windows','WindowsController@getIndex');
    Route::post('windows','WindowsController@postStore');
    Route::get('windows/data','WindowsController@getData');
    Route::post('windows/car_models','WindowsController@getCarModels');
    Route::post('windows/car_bodies','WindowsController@getCarBodies');
    Route::post('windows/car_producers','WindowsController@getCarProducers');
    Route::post('windows/window_types','WindowsController@getWindowTypes');
    Route::match(['get', 'post'], '/unrecognized', 'UnrecognizedController');
});


Route::get('/', 'HomeController@index')->name('home');

Auth::routes();
