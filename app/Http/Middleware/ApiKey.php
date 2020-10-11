<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class ApiKey
{
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
            return response()->json([
                'message' => 'Unauthorized',
            ],401);
        }

        return $next($request);
    }
}
