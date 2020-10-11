<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/users/{user}','UserController@getUser')->name('get.user');
