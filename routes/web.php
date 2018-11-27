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

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


Route::get('/home', 'HomeController@index')->name('home');
Route::prefix('admin')->name('admin.')->namespace('Admin')->middleware('auth')->group(function () {
    Route::prefix('user')->name('user.')->middleware('can:create_users,App\User')->group(function(){
        Route::get('/', 'UserController@index')->name('index');
        Route::post('/', 'UserController@store')->name('store');
        Route::put('/{id}', 'UserController@update')->name('update');
        Route::put('/{id}/toggle_active', 'UserController@toggle_active')->name('toggle');
    });

   
});
