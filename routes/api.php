<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//header('Access-Control-Allow-Origin: *');
Route::post('/', 'MasterController@index');
Route::get('/sync_users', 'MasterController@sync');
Route::get('/retrieve_patients', 'MasterController@syncPatients');

//Route::post('/sendSms', 'SMSController@index');
Route::post('/receiveSms', 'SMSController@index');

Route::post('/login', 'UserController@login');
Route::post('/registration', 'UserController@registration');
