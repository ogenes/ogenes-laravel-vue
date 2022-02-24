<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/17
 */

namespace App\Exceptions;


use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class CommonException extends Exception
{
    public function __construct($code = ErrorCode::SYSTEM, $msg = '')
    {
        $msg || $msg = ErrorCode::getMsg($code);
        Log::warning("CommonException", [
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'code' => $code,
            'msg' => $msg,
            'trace' => $this->getTraceAsString(),
        ]);
        
        throw (new HttpResponseException(response()->json([
            'code' => $code,
            'msg' => $msg,
        ], 200)));
    }
}
