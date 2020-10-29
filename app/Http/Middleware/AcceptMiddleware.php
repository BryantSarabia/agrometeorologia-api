<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ResponsesJSON;

class AcceptMiddleware
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

        $accept = $request->header('Accept');
        if($accept !== 'application/json'){
            return $this->ResponseError(406,'Not Acceptable','Be sure to set the header "Accept" to "application/json"');
        }

    }
}
