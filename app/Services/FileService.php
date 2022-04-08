<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/8
 */

namespace App\Services;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Helpers\AliOssHelper;
use App\Models\FileUpload;
use Illuminate\Http\UploadedFile;

class FileService extends BaseService
{
    public const ALLOW_TYPE = [
        'png',
        'jpg',
        'jpeg',
        'bmp',
        'gif',
        'tiff',
        'svg',
        'rar',
        'zip',
        'xlsx',
        'pdf',
    ];
    
    public function upload(UploadedFile $file, string $source): array
    {
        $tmpFile = $file->getRealPath();
        if (!file_exists($tmpFile)) {
            throw new CommonException(ErrorCode::SYSTEM, 'File not exists');
        }
        
        $objectId = sha1_file($tmpFile);
        $exists = FileUpload::whereUid($this->uid)
            ->where('source', '=', $source)
            ->where('object_id', '=', $objectId)
            ->first();
        if ($exists) {
            if ($exists->file_status <= 0) {
                $exists->file_status = 1;
                $exists->save();
            }
            return $exists->toArray();
        }
        
        $ext = $file->getClientOriginalExtension() ? : 'png';
        $ext = strtolower($ext);
        if (!in_array($ext, self::ALLOW_TYPE, true)) {
            throw new CommonException(ErrorCode::INVALID_FILE_TYPE);
        }
        
        try {
            $savePath = 'img' . date('/y/m/d/') . $objectId . '.' . $ext;
            $data = [];
            $data['uid'] = $this->uid;
            $data['source'] = $source;
            $data['original_name'] = $file->getClientOriginalName();
            $data['object_id'] = $objectId;
            $data['path'] = AliOssHelper::getInstance()->upload($tmpFile, $savePath);
            $data['size'] = $file->getSize();
            $data['ext'] = $ext;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['id'] = FileUpload::insertGetId($data);
            return $data;
        } catch (\Exception $e) {
            throw new CommonException(ErrorCode::UPLOAD_FAILED);
        }
    }
    
}
