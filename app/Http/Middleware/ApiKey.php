<?php

namespace App\Http\Middleware;

use App\Jobs\LogProjectRequestJob;
use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use App\Traits\ResponsesJSON;


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

        if($token == null){
            return $this->ResponseError(401,'Unauthorized','Incorrect API Key, please try again');
        }

        $project = Project::where('api_key',$token)->first();

        if($project == null ){
            return $this->ResponseError(401,'Unauthorized','Incorrect API Key, please try again');
        }

        return $next($request);
    }

    public function terminate($request, $response){
        if($request->bearerToken()) {
            LogProjectRequestJob::dispatch('/' . $request->path(), $request->bearerToken());
        }
    }
}
