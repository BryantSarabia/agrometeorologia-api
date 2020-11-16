<?php

use App\Mail\PestReports;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
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
})->name('report.create')->middleware('auth');

Route::get('reports',function(){
    return view('pages.reports');
})->name('report.index')->middleware('auth');

Route::get('me/locations',function(){
    return view('pages.locations');
})->name('me.locations')->middleware('auth');


Route::get('email', function(){
    $user = User::find(5);
    $reports = \App\Models\Report::all();
    return new \App\Mail\PestReports($user, $reports);
});

Route::get('prova', function(){
    $users = User::all();
    $users->each(function ($user) {
        if ($user->locations->count() > 0) {
            $reports = collect();
            $user->locations->each(function ($location) use ($reports) {
                $reports->push($location->findNearestReports($location->lat, $location->lon, $location->radius));
            });

            Mail::to($user)->send(new PestReports($user, $reports->first()->unique()));
        }
    });
});
