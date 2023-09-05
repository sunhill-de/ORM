<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/ajax/getItem/{item}', 'Sunhill\ORM\Controllers\AjaxController@getItem');
Route::any('/ajax/getNodes/{parent}', 'Sunhill\ORM\Controllers\AjaxController@getNodes');
Route::any('/ajax/getNodes', 'Sunhill\ORM\Controllers\AjaxController@getNodes');
