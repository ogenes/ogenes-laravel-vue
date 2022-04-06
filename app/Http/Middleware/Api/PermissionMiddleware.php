<?php

namespace App\Http\Middleware\Api;

use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Services\RoleService;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * 存在接口允许多个菜单权限访问时在此添加配置
     */
    public const MENUS_MAP = [
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws CommonException
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        if (is_object($route) && method_exists($route, 'getName')) {
            $routeName = $route->getName();
            $codes = self::MENUS_MAP[$routeName] ?? [$routeName];
            $service = RoleService::getInstance();
            $id = $service->uid;
            $menus = $service->getUserHasMenus($id, 1);
            
            $menuCodes = array_column($menus, 'menuName');
            if (empty(array_intersect($codes, $menuCodes))) {
                throw new CommonException(ErrorCode::INVALID_ROLES);
            }
        }
        return $next($request);
    }
}
