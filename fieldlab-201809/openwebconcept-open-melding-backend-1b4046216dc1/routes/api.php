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

Route::get('/notifications', 'API\NotificationController@get');
Route::post('/notifications', 'API\NotificationController@store');

Route::get('/municipality/{slug}', 'API\MunicipalityController@get');
Route::get('/municipality/{slug}/notifications', 'API\MunicipalityController@getNotifications');
