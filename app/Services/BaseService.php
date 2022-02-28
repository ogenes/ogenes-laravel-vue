<?php

/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2021/7/13
 */

namespace App\Services;


use Illuminate\Redis\Connections\Connection as RedisConnection;
use Illuminate\Support\Facades\Redis;

class BaseService
{
    /**
     * @var array
     */
    protected static array $instance = [];

    /**
     * @return static
     */
    public static function getInstance(): BaseService
    {
        $className = static::class;
        if (isset(self::$instance[$className])) {
            return self::$instance[$className];
        }
        self::$instance[$className] = new static();
        if (!self::$instance[$className] instanceof AuthService) {
            self::$instance[$className]->uid = AuthService::getInstance()->uid;
        }
        return self::$instance[$className];
    }

    /**
     * @var int
     */
    public int $uid = 0;
    
    /**
     * @return RedisConnection
     */
    public function getRedis(): RedisConnection
    {
        return Redis::connection('default');
    }

}
