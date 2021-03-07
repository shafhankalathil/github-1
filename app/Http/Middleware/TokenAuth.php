<?php

namespace App\Http\Middleware;

use Closure;

class TokenAuth
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
        if($request->header('apiKey') != 'b103b410-e498-11ea-9ecf-99c82d4700ea'){
            return response()->json(['error'=>'Invalid API Key'], 401);
        }
        return $next($request);
    }
}
