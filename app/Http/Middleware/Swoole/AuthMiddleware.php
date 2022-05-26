<?php

namespace App\Http\Middleware\Swoole;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;


class AuthMiddleware
{
    

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     *
     */
    public function handle(Request $request, Closure $next)
    {
        $token =$request->query('Authorization');
        if ($token && AuthService::getInstance()->checkLogin($token)) {
            echo 'AUTH-UID' . AuthService::getInstance()->uid;
            Websocket::loginUsingId(AuthService::getInstance()->uid);
        }
        return $next($request);
    }
}
