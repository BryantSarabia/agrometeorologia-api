<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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


Route::get('email', function(){
    $user = User::where('token', '0uz1yEwSY30IkdKFdnHb8oluRu70B3xEn4iXO9Om')->first();
    return $user;
});

//Route::get('prova', function(){
//    $users = User::all();
//    $users->each(function ($user) {
//        if ($user->locations->count() > 0) {
//            $reports = collect();
//            $user->locations->each(function ($location) use ($reports) {
//                $reports->push($location->findNearestReports($location->lat, $location->lon, $location->radius));
//            });
//
//            Mail::to($user)->send(new PestReports($user, $reports->first()->unique()));
//        }
//    });
//});
