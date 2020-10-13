<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ResponsesJSON;
use Illuminate\Http\Request;

class headersMiddleware
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
        if($content_type !== 'application/vnd.api+json'){
           return $this->ResponseError(415,'Unsupported Media Type');
        }

        $accept = $request->header('Accept');
        if($accept !== 'application/vnd.api+json'){
           return $this->ResponseError(406,'Not Acceptable');
        }
        return $next($request);
    }
}
