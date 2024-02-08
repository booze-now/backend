<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageChooser
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
        if ($request->has('lang')) {

            $supportedLanguages = config('app.available_locales');
            $lang = $request->query('lang');

            if(in_array($lang, $supportedLanguages)) {
                $lang = $request->query('lang');
            } else {
                return response()->json(['error' => 'Language is not supported'], 400);
            }
            App::setLocale($lang);
        }
        return $next($request);
    }
}
