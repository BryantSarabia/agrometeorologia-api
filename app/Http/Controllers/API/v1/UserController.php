<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponsesJSON;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ResponsesJSON;
    public function authenticate(Request $request){
        if(!$request->email || !$request->password){
            return $this->ResponseError(400, 'Bad request', 'Missing parameters');
        }

        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            return $this->ResponseError(400, 'Bad request', 'Email is not valid');
        }

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return $this->ResponseError(404, 'Not found', 'This user does not exist');
        }

        if(!Hash::check($request->password, $user->password)){
            return $this->ResponseError(401, 'Unauthorized', 'The password is invalid');
        }

        $user->generateToken();

        return response()->json([
            'token' => $user->token
        ],200,['Content-Type' => 'application/json']);

    }

    public function register(Request $request){

        if(!$request->email){
            return $this->ResponseError(400, 'Bad request', 'Missing Email');
        }

        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            return $this->ResponseError(400, 'Bad request', 'Email is not valid');
        }

        if(User::where('email',$request->email)->first()){
            return $this->ResponseError(400, 'Bad request', 'Email is already taken');
        }

        if(!$request->name){
            return $this->ResponseError(400, 'Bad request', 'Missing name');
        }

        if(!$request->password){
            return $this->ResponseError(400, 'Bad request', 'Missing password');
        }

        if(!$request->password_confirmation){
            return $this->ResponseError(400, 'Bad request', 'Missing password confirmation');
        }

        if($request->password !== $request->password_confirmation){
            return $this->ResponseError(400, 'Bad request', 'The password confirmation does not match');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->generateToken();
        $user->save();

        return response()->json([
            'token' => $user->token
        ],201, ['Content-Type' => 'application/json']);
    }

    public function logout(Request $request){

        $user = User::where('token',$request->bearerToken())->first();
        $user->token = null;
        $user->save();
        return response()->json([],204);
    }
}
