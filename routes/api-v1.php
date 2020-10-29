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
