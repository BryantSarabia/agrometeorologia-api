<?php
use Illuminate\Support\Facades\Route;

Route::get('', 'AdminController@home')->name('admin.home');

Route::get('configuration/create', 'ConfigurationController@create')->name('admin.configuration.create');
Route::post('configuration/save', 'ConfigurationController@save')->name('admin.configuration.save');
Route::get('configuration/all', 'ConfigurationController@index')->name('admin.configuration.all');
Route::get('configuration/{id}','ConfigurationController@show')->name('admin.configuration.show');
Route::get('configuration/{id}/specification','ConfigurationController@download_specification')->name('admin.configuration.download.specification');
Route::get('user/all', 'AdminController@users')->name('admin.user.all');
Route::get('project/all','AdminController@projects')->name('admin.project.all');
Route::delete('project/{id}', 'AdminController@projectDelete')->name('admin.project.delete');
Route::delete('user/{id}', 'AdminController@userDelete')->name('admin.user.delete');
Route::get('analytics','AdminController@analytics')->name('admin.dashboard.analytics');
Route::get('prova', 'AdminController@analytics');
