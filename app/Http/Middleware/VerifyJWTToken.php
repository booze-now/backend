<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('token')) {
            try {

                $auth = null;
                if ($request->is('api/staff/*')) {
                    $auth = Auth::guard('guard_employee');
                } elseif ($request->is('api/guest/*')) {
                    $auth = Auth::guard('guard_guest');
                } else {
                    return response(__('Forbidden.'), 403);
                }

                // $token = Auth::guard('guard_employee')->authenticate();
                $token = $auth->parseToken();
                $customClaimsKeys = array_keys($auth->user()->getJWTCustomClaims());
                $customClaims = array_combine($customClaimsKeys, $token->getClaim($customClaimsKeys));

                if (!$auth->user()->checkCustomClaims($customClaims)) {
                    return response(__('Forbidden.'), 403);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $next($request);
    }
}
