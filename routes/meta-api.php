<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('services', 'MetaController@save')->name('service.register');

Route::get('services/fakeapi/posts/all','MetaController@get');
Route::get('services/{configuration}','MetaController@delete')->name('service.delete');
