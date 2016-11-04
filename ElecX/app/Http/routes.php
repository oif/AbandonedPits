<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/join', function () {
    return view('join')->withFlag(false);
});

Route::get('/inquiry', function () {
    return view('inquiry')->withFlag(false);
});

Route::get('/code', 'UsersController@code');

Route::post('/join', 'UsersController@join');
Route::post('inquiry', 'UsersController@inquiry');
Route::get('/workflowStart', 'MonitorController@workflowStart');
Route::get('/notificationStart', 'MonitorController@notificationStart');