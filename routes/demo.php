<?php

/* Demo */

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function(){
    return view('demo.home');
})->name('demo.home');

Route::get('reports/create',function(){

    return view('demo.pages.create_report');
})->name('demo.report.create')->middleware('auth');

Route::get('reports',function(){

    return view('demo.pages.reports');
})->name('demo.report.index')->middleware('auth');

Route::get('locations',function(){

    return view('demo.pages.locations');
})->name('demo.locations');

Route::get('login', function(){
    return view('demo.pages.login');
})->name('demo.login');

Route::post('login','AuthController@authenticate')->name('demo.authenticate');

Route::get('register', function(){
    return view('demo.pages.register');
})->name('demo.register');

Route::post('register', 'AuthController@register')->name('demo.user.register');

Route::post('logout','AuthController@logout')->name('demo.logout')->middleware('auth');
