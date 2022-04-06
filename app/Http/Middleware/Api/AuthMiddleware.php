<?php

/**
 * Created by cynic-image
 * User: ogenes
 * Date: 2021/12/29
 */

namespace App\Http\Middleware\Api;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Services\AuthService;
use Closure;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws CommonException
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        if (empty($token) || !AuthService::getInstance()->checkLogin($token)) {
            throw new CommonException(ErrorCode::LOGIN_REQUIRED);
        }
        return $next($request);
    }
}
