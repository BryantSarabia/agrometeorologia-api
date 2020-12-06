<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('services', 'MetaController@save')->name('service.register');
Route::get('services/{group}/{service}/{operation}','MetaController@get')->name('service.process');
Route::delete('services/{id}','MetaController@delete')->name('service.delete');
Route::patch('services/{id}','MetaController@toggle')->name('service.patch');


