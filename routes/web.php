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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/error', 'SpaController@index')->name('error');
Route::get('/logout', 'AuthController@logout');

Route::middleware('registration.progress')->group(function () {
    Route::get('/personal-data/{user_type}', 'SpaController@index');
    Route::get('/citizen/{user_type}', 'SpaController@index');
    Route::get('/register/{user_type}', 'SpaController@index');
    //Route::get('/verify/{user_type}/{phone_call}', 'SpaController@index');
    Route::get('/offer/{user_type}', 'SpaController@index');
    Route::get('/rules/{user_type}', 'SpaController@index');
    Route::get('/link', 'SpaController@index');
});


Route::middleware('check.privilege')->group(function () {
    Route::get('/accountancy/{any}', 'SpaController@index')->where('any', '.*');
    Route::get('/{any}', 'SpaController@index')->where('any', '.*');
});

//Auth::routes();

