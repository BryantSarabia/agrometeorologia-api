<?php

use Illuminate\Support\Facades\Route;



Auth::routes();

Route::get('/','HomeController@index')->name('home');
Route::get('api/specification', function (){
    return view('swagger.specification');
})->name('api.specification');

Route::get('projects','ProjectController@index')->name('project.index');
Route::get('projects/create','ProjectController@create')->name('project.create');
Route::post('projects/save','ProjectController@save')->name('project.save');
Route::delete('projects/{project}/delete','ProjectController@delete')->name('project.delete');
Route::post('projects/{project}/token','ProjectController@token')->name('project.token');

Route::get('reports/create',function(){
    return view('pages.create_report');
})->name('report.create');

Route::get('reports',function(){
    return view('pages.reports');
})->name('report.index');

Route::get('me/locations',function(){
    return view('pages.locations');
})->name('me.locations');


