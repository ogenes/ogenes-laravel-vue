<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/4
 */

namespace App\Services;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Helpers\OssHelper;
use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    
    public function upload(UploadedFile $file): array
    {
        $tmpFile = $file->getRealPath();
        if (!file_exists($tmpFile)) {
            throw new CommonException(ErrorCode::SYSTEM, 'File not exists');
        }
        
        $objectId = sha1_file($tmpFile);
        
        $ext = $file->getClientOriginalExtension();
        $ext = strtolower($ext);
        
        if (!in_array($ext, self::ALLOW_TYPE, true)) {
            throw new CommonException(ErrorCode::INVALID_FILE_TYPE);
        }
        
        try {
            $savePath = 'img' . date('/y/m/d/') . $objectId . '.' . $ext;
            $data = [];
            $data['uid'] = $this->userId;
            $data['original_name'] = $file->getClientOriginalName();
            $data['object_id'] = $objectId;
            $data['path'] = OssHelper::getInstance()->uploadAli($tmpFile, $savePath);
            $data['size'] = $file->getSize();
            $data['ext'] = $ext;
            $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
            $data['id'] = File::insertGetId($data);
            return $data;
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'msg' => $e->getMessage()
            ]);
            throw new CommonException(ErrorCode::UPLOAD_FAILED);
        }
    }
    
    public function getList(string $keyword, array $uploadDate, int $page = 1, int $pageSize = 30): array
    {
        if ($this->userId <= 0) {
            return [];
        }
        $fr = (new File())->getTable();
        $fields = [
            DB::raw('fr.id as file_id'),
            DB::raw('fr.original_name as name'),
            'fr.object_id',
            'fr.path',
            'fr.size',
            'fr.ext',
            'fr.created_at',
            'fr.updated_at',
        ];
        $query = File::from("{$fr} as fr")
            ->where('fr.uid', '=', $this->userId)
            ->when(!empty($keyword), static function (Builder $query) use ($keyword) {
                return $query->where('fr.original_name', 'like', "%{$keyword}%");
            })
            ->when(!empty($uploadDate), static function (Builder $query) use ($uploadDate) {
                return $query->where('fr.updated_at', '>=', "{$uploadDate[0]} 00:00:00")
                    ->where('fr.updated_at', '<=', "{$uploadDate[1]} 23:59:59");
            });
        $paginateData = $query->orderBy('fr.updated_at', 'desc')
            ->orderBy('fr.id', 'desc')
            ->paginate($pageSize, $fields, 'page', $page)
            ->toArray();
        $ret['page'] = $page;
        $ret['pageSize'] = $pageSize;
        $ret['total'] = $paginateData['total'] ?? 0;
        $ret['list'] = $paginateData['data'] ?? [];
        foreach ($ret['list'] as $key => $item) {
            $ret['list'][$key]['url'] = $item['path'];
        }
        return $ret;
    }
    
    public function sync(array $files): bool
    {
        if ($this->userId <= 0) {
            return false;
        }
        
        foreach ($files as $item) {
            $id = $item['id'] ?? 0;
            $objectId = $item['objectId'] ?? '';
            if (!($id && $objectId)) {
                continue;
            }
            File::whereId($id)
                ->where('object_id', '=', $objectId)
                ->where('uid', '=', 0)
                ->where('state', '=', 0)
                ->update([
                    'uid' => $this->userId
                ]);
        }
        return true;
        
    }
}
