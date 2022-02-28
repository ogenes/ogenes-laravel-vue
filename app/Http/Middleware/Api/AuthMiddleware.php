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
    
    public const NO_AUTH_ROUTES = [
        'file.upload',
        'system.init',
        'system.setLang',
    ];
    
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
            $route = $request->route();
            if (is_object($route) && method_exists($route, 'getName')) {
                $routerName = $route->getName();
            } else {
                $routerName = '';
            }
            if (!in_array($routerName, self::NO_AUTH_ROUTES, true)) {
                throw new CommonException(ErrorCode::LOGIN_REQUIRED);
            }
        }
        return $next($request);
    }
}
