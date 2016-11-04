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

Route::get('/dynamic/robot', 'DynamicsController@dynamic'); // Dynamic robot
Route::get('/stat', 'InterfacesController@systemStat'); // System stat
Route::post('/rewards.json', 'InterfacesController@rewards');   // Rewards
Route::post('/dynamic/alerts.json', 'InterfacesController@alerts'); // Alerts in progress
Route::post('/dynamic/invasions.json', 'InterfacesController@invasions');   // Invasions in progress
Route::post('/history/alerts.json', 'InterfacesController@alertsHistory');  // Expired alerts
Route::post('/history/invasions.json', 'InterfacesController@invasionsHistory');    // Completed invasions

//Trans
Route::get('/trans', 'TransController@index');
Route::get('/keng', 'TransController@keng');
Route::get('/transShow', 'TransController@show');
Route::post('/updateTrans', 'TransController@update');
