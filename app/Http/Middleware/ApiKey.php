<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use App\Traits\ResponsesJSON;
use Illuminate\Support\Facades\Crypt;


class ApiKey
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

        $project = Project::where('api_key',$token)->first();

        if(!$project){
            return $this->ResponseError(401,'Unauthorized','Incorrect API Key, please try again');
        }

        return $next($request);
    }
}
