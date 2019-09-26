<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $token = $request->header('Authorization');
        if(!empty(trim($token))){
            $user = User::where('api_token' , $token)->first();
            if($user){
                Auth::login($user);
                return $next($request);
            }
        }
        return response()->json('Invalid Token', 401);
    }
}
