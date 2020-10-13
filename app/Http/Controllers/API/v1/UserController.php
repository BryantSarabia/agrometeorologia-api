<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function show(User $user)
    {
        return UserResource::make($user->load('projects'));
    }

    public function index()
    {
        return UserCollection::make(User::with('projects')->get());
    }

}
