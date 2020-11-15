<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//
//Route::get('users/{user}','UserController@show')->name('api.v1.users.show');
//Route::get('users','UserController@index')->name('api.v1.users.index');
//
//
//Route::get('projects','ProjectController@index')->name('api.v1.projects.index');
//Route::get('projects/{project}','ProjectController@show')->name('api.v1.projects.show');
//Route::get('projects/{project}/requests','RequestController@requests')->name('api.v1.projects.requests');
//
//
//Route::get('requests','RequestController@index')->name('api.v1.requests.index');
//Route::get('requests/{request}','RequestController@show')->name('api.v1.requests.show');

/* API */

/******* Stations Tag ******/
Route::get('stations','StationController@getStations')->name('api.v1.stations.getStations');
Route::get('stations/{id}', 'StationController@getStation')->name('api.v1.stations.getStation');

/******** Weather Tag ******/
Route::get('stations/{id}/weather', 'WeatherController@getStationWeather')->name('api.v1.stations.weather');

/******** Indicator Tag ************/
Route::get('indicators','IndicatorController@getIndicators')->name('api.v1.indicators.getIndicators');
Route::get('indicators/{id}','IndicatorController@getIndicator')->name('api.v1.indicators.getIndicator');
Route::get('stations/indicators/{id}','IndicatorController@getIndicatorValues')->name('api.v1.indicators.getIndicatorValues');
Route::get('stations/{station_id}/indicators/{indicator_id}','IndicatorController@getIndicatorValue')->name('api.v1.indicators.getIndicatorValue');

/******** Models Tag *********/
Route::get('models','ModelController@getModels')->name('api.v1.models.getModels');
Route::get('stations/{station_id}/models/{model_name}','ModelController@runModel')->name('api.v1.models.runModel');

/********** Pests Tag ***********/
Route::post('pests/reports','PestController@report')->name('api.v1.pests.report');
Route::get('pests/reports','PestController@getReports')->name('api.v1.pests.getReports'); // Date le coordinate ed il raggio ritorna le segnalazioni d'interesse
Route::get('reports','PestController@index')->name('api.v1.pest.reports.index'); // Ritorna tutte le segnalazioni nell'ultimo mese.

Route::get('me/locations', 'LocationController@index')->name('api.v1.me.locations.index');
Route::delete('me/locations', 'LocationController@deleteAll')->name('api.v1.me.locations.deleteAll');
Route::post('me/locations','LocationController@save')->name('api.v1.me.locations.save');
Route::delete('me/locations/{id}', 'LocationController@delete')->name('api.v1.me.locations.delete');
