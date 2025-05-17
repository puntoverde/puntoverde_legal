<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request); 
        if(Str::of($response->headers->get('Content-Type'))->exactly('application/json') && $response->status()<300)
        return response()->json($response->getOriginalContent()->toArray())->setEncodingOptions(JSON_NUMERIC_CHECK);
        else return $response;
    }
}

