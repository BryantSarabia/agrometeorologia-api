<?php
use Illuminate\Support\Facades\Route;

Route::get('', 'AdminController@home')->name('admin.home');

Route::get('configuration/create', 'ConfigurationController@create')->name('admin.configuration.create');
Route::post('configuration/save', 'ConfigurationController@save')->name('admin.configuration.save');
Route::get('configuration/all', 'ConfigurationController@index')->name('admin.configuration.all');
Route::get('configuration/{id}/show','ConfigurationController@show')->name('admin.configuration.show');
