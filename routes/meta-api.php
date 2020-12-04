<?php
namespace routes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('services', 'MetaController@save')->name('register.service');
Route::get('/services/{group}/{service}/{operation}','MetaController@get')->name('service.get');
Route::get('/services/meteo/forecasts/rainForecast','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/fakeapi/posts/all','MetaController@get');
Route::get('/services/myapi/stations/all','MetaController@get');
