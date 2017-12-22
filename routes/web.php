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

Route::group(['prefix' => 'dapenti', 'as' => 'dapenti.'], function ()  {
	Route::get('/', ['as' => 'index', 'uses' => 'DapentiController@index']);
	Route::get('list/{type}', ['as' => 'list', 'uses' => 'DapentiController@list']);
	Route::get('image', ['as' => 'image', 'uses' => 'DapentiController@image']);
	Route::get('show/{id}', ['as' => 'show', 'uses' => 'DapentiController@show']);
});