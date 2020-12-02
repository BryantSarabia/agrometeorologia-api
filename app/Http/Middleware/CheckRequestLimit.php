<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Traits\ResponsesJSON;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class CheckRequestLimit
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

        $project = Project::where('api_key', $request->bearerToken())->first();
        $count = $project->requests->where('date', '>=', date('Y-m-01'))->where('date','<=',date('Y-m-t'))->sum('number');
        if ($count <= $project->getLicenseRateLimit()) {
            return $next($request);
        }

        // Count is equal to (or greater than) limit; return error 429
        $retryAfter = date('Y-m-d', strtotime(date('Y-m-01') . ' + 1 month'));
        $message = 'You have exceeded your monthly limit, please retry after: ' . $retryAfter;

        return $this->ResponseError(429,'Too many requests', $message);
    }
}
