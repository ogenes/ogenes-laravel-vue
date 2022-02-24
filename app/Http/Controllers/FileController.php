<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/4
 */

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
        $ret = FileService::getInstance()->upload($file);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    /**
     * 私人文件列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        $keyword = $request->input('keyword', '') ?? '';
        $uploadDate = array_filter($request->input('uploadDate', []) ?? []);
        $page = $request->input('page', 1) ?? 1;
        $pageSize = $request->input('pageSize', 10) ?? 10;
        $ret = FileService::getInstance()->getList($keyword, $uploadDate, $page, $pageSize);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    /**
     * 无归属的文件同步
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        $files = $request->input('files', '') ?? '';
        $ret = FileService::getInstance()->sync($files);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
