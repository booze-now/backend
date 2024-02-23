<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uuid = Str::uuid();
        $data = [
            'Request Method' => $request->method(),
            'Request Path' => $request->path(),
            'Requesting User' => $request->user()? $request->user()->toArray(): "none",
            'Request Params' => $request->all(),
            'Request IP' => $request->ip(),
            'Request URI' => $request->getRequestUri(),
            'lang' => $request->getLanguages(),
            'Origin' => $request->header('host'),
        ];

        Log::channel('requests')->info(json_encode($request->headers->all(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        Log::channel('requests')->info($uuid . ":\n" . json_encode($data, JSON_UNESCAPED_SLASHES| JSON_PRETTY_PRINT));

        $ret = $next($request);

        Log::channel('requests')->info($uuid . ":\n" . json_encode($ret, JSON_UNESCAPED_SLASHES| JSON_PRETTY_PRINT));

        return $ret;
    }
}
