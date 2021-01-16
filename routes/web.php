<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Auth::routes();

Route::get('/','HomeController@index')->name('home');

Route::get('projects/all','ProjectController@index')->name('project.index');
Route::get('projects/create','ProjectController@create')->name('project.create');
Route::post('projects/save','ProjectController@save')->name('project.save');
Route::delete('projects/{project}/delete','ProjectController@delete')->name('project.delete');
Route::post('projects/{project}/token','ProjectController@token')->name('project.token');
Route::get('specifications/all', 'SpecificationController@index')->name('api.specification.index');
Route::get('specifications/{id?}', 'SpecificationController@show')->name('api.specification.show');
