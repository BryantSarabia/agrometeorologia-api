<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\ResponsesJSON;
use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    use ResponsesJSON;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $token = $request->bearerToken();

        if($token == null){
            return $this->ResponseError(401,'Unauthorized','Incorrect token, please try again');
        }

        $user = User::where('token', $token)->first();

        if($user == null){
            return $this->ResponseError(401,'Unauthorized','Incorrect token, please try again');
        } elseif(!$user->isAdmin()){
            return $this->ResponseError(401,'Unauthorized','Only administrators can access');
        }
        return $next($request);
    }
}
