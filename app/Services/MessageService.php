<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;

class MessageService extends BaseService
{
    public const
        CAT_SYSTEM = 1,
        CAT_NOTIFY = 2,
        CAT_DYNAMIC = 3,
        CAT_MAP = [
        self::CAT_SYSTEM => '公司制度',
        self::CAT_NOTIFY => '公告通知',
        self::CAT_DYNAMIC => '公司动态',
    ];
    
    public function getList(string $keyword, int $page = 1, int $pageSize = 30): array
    {
        $resp = Message::select()
            ->where('title', 'like', "%{$keyword}%")
            ->orWhere('desc', 'like', "%{$keyword}%")
            ->orWhere('text', 'like', "%{$keyword}%")
            ->orderBy('id', 'desc')
            ->paginate($pageSize, ['*'], 'page', $page)
            ->toArray();
        $ret['cnt'] = $resp['total'];
        $ret['page'] = $resp['current_page'];
        $ret['pageSize'] = $resp['per_page'];
        foreach ($resp['data'] as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $item['cat'] = self::CAT_MAP[$item['cat_id']] ?? '';
            $tmp = [];
            foreach ($item as $key => $value) {
                $tmp[Str::camel($key)] = $value;
            }
            $ret['list'][] = $tmp;
        }
        return $ret;
    }
    
    public function getDetail(int $id): array
    {
        $resp = Message::whereId($id)->first();
        $ret = [];
        if ($resp) {
            $data = $resp->toArray();
            $data['created_at'] = formatDateTime($data['created_at']);
            $data['updated_at'] = formatDateTime($data['updated_at']);
            foreach ($data as $key => $value) {
                $ret[Str::camel($key)] = $value;
            }
        }
        return $ret;
    }
    
    public function save(Message $obj): array
    {
        $ret = [];
        DB::beginTransaction();
        try {
            if ($obj->id > 0) {
                $exist = Message::whereId($obj->id)->first();
                if ($exist) {
                    $updateData = [];
                    foreach ($obj->getAttributes() as $k => $v) {
                        $v !== $exist->$k && $updateData[$k] = $v;
                    }
                    if ($updateData) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        foreach ($updateData as $key => $val) {
                            $exist->setAttribute($key, $val);
                        }
                        $exist->save();
                        ActionLogService::getInstance()->insert(
                            ActionLogService::RESOURCE_MSG,
                            $obj->id,
                            $this->uid,
                            '编辑',
                            $updateData
                        );
                    }
                }
                $data = $exist->toArray();
            } else {
                $obj->created_at = $obj->updated_at = date('Y-m-d H:i:s');
                $obj->author_id = $this->uid;
                $obj->save();
                $id = $obj->id;
                $data = $obj->getAttributes();
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_MSG,
                    $id,
                    $this->uid,
                    '新增',
                    $data
                );
            }
            $data['created_at'] = formatDateTime($data['created_at']);
            $data['updated_at'] = formatDateTime($data['updated_at']);
            foreach ($data as $key => $value) {
                $ret[Str::camel($key)] = $value;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $ret;
    }
}
