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
Route::get('/send_feedback', 'FeedbackController@send');

//Route::post('/sendSms', 'SMSController@index');
Route::post('/receiveSms', 'SMSController@index');

Route::post('/login', 'UserController@login');
Route::post('/registration', 'UserController@registration');
Route::post('/reset_pin', 'UserController@resetPin');
Route::post('/change_pin', 'UserController@changePin');
Route::post('/dependents', 'UserController@dependents');
Route::post('/add_dependent', 'UserController@addDependent');
Route::post('/delete_dependent', 'UserController@deleteDependent');

Route::post('/share_records', 'PatientController@shareProfile');
Route::post('/last_visit', 'PatientController@lastVisit');
Route::post('/full_history', 'PatientController@fullHistory');
Route::post('/patient_profile', 'PatientController@patientProfile');
Route::post('/forget_patient', 'PatientController@forgetPatient');

Route::post('/feedback', 'FeedbackController@index');
