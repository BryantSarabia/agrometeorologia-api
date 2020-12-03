<?php
namespace routes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('services', 'MetaController@save')->name('register.service');
Route::get('/services/{group}/{service}/{operation}','MetaController@get')->name('service.get');
