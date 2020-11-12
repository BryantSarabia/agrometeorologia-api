<?php

use Illuminate\Support\Facades\Route;



Auth::routes();


Route::get('/','ProjectController@index')->name('project.index');
Route::get('project/create','ProjectController@create')->name('project.create');
Route::post('project/save','ProjectController@save')->name('project.save');
Route::delete('project/{project}/delete','ProjectController@delete')->name('project.delete');
Route::post('project/{project}/token','ProjectController@token')->name('project.token');


Route::get('api/specification', function (){
    return view('swagger.specification');
})->name('api.specification');
