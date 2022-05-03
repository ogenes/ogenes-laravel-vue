<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/5/3
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class AliOssFacade extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'aliOss';
    }
}
