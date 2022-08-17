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

Auth::routes();

Route::get('/admin-tours/', 'BackendController@index')->name('admin')->middleware('auth');;
Route::get('/admin-tours/{all}', function(){
    return redirect()->route('admin');
})->where('all', '.*');

Route::get('/home', 'HomeController@index')->name('home');
