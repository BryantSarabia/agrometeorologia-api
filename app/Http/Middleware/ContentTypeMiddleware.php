<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ResponsesJSON;
use Illuminate\Http\Request;

class contentTypeMiddleware
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

        $content_type = $request->header('Content-Type');
        if($content_type !== 'application/json'){
           return $this->ResponseError(415,'Unsupported Media Type','Be sure to set the header "Content-Type" to "application/json"');
        }

        return $next($request);
    }
}
