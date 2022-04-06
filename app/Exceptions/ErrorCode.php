<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/17
 */

namespace App\Exceptions;


use ReflectionClass;

/**
 * Class ErrorCode
 *
 * @package App\Exceptions
 */
class ErrorCode
{
    public const UNKNOW = 1000;
    public const SYSTEM = 1001;
    public const INVALID_ARGUMENT = 1002;
    public const INVALID_ROLES = 1003;
    public const LOGIN_REQUIRED = 1004;
    public const PASSWORD_ERROR = 1005;
    public const EMAIL_EXISTS = 1007;
    public const LOGIN_FAILED = 1008;
    public const NO_USER_FOUND = 1009;
    public const VERIFICATION_CODE_ERROR = 1010;
    public const UPLOAD_FAILED = 1011;
    public const INVALID_FILE_TYPE = 1012;
    public const MOBILE_EXISTS = 1013;
    public const RECORD_EXCEPTION = 1014;
    public const RECORD_EXISTS = 1015;
    
    public static function getMsg($errorCode): string
    {
        $const = (new ReflectionClass(self::class))->getConstants();
        $key = array_search($errorCode, $const, true) ? : 'UNKNOW';
        return trans("error.{$key}");
    }
}
