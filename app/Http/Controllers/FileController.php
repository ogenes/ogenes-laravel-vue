<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * 上传文件
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CommonException
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $source = $request->input('source');
        $ret = FileService::getInstance()->upload($file, $source);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
