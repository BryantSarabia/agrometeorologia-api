<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    public function authenticate(Request $request){

        $validator = $request->validate([
           'email' => 'email|required',
            'password' => 'string|required'
        ]);

        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials)){
            return redirect()->route('demo.home');
        }

    }

    public function register(Request $request){

        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data = $request->all();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->generateToken();
        $user->save();
        Auth::login($user);
        return redirect()->route('demo.home');

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('demo.home');
    }
}
