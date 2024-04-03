<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->hasHeader('origin')) {
            $origin = $request->header('origin');
        } else {
            $origin = '*';
        }

        if ($request->isMethod('OPTIONS')) {

            $response = response('', 200)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');
            return $response;
        } else {
            $response = $next($request);
            if ($request->hasHeader('origin')) {
                $response->header('Access-Control-Allow-Origin', $origin);
            }
            return $response;
        }

        // $response = $next($request);
        // if ($request->hasHeader('origin')) {
        //     $response->header('Access-Control-Allow-Origin', $request->header('origin'));
        // }

        // if (strtoupper($request->method()) == 'OPTIONS') {
        //     $response->header('Access-Control-Allow-Methods', $request->header('access-control-request-method'))
        //         ->header('Access-Control-Allow-Headers', $request->header('access-control-request-headers'))
        //         // ->header('Access-Control-Allow-Origin', '*')
        //         // ->header('Access-Control-Allow-Origin', 'http://localhost:3000/')
        //         // ->header('Access-Control-Allow-Credentials', 'true')
        //         // ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        //         // ->header('Access-Control-Allow-Headers', 'Origin, Accept, X-Requested-With, Content-Type, X-Token-Auth, Authorization')
        //     ;
        // }
        // return $response;
    }
}
