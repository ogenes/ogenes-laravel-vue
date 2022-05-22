<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Models\Message;
use App\Models\Role;
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
    
    public function getList(string $keyword, array $sort, int $page = 1, int $pageSize = 30): array
    {
        $query = Message::select();
        if ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")
                ->orWhere('desc', 'like', "%{$keyword}%")
                ->orWhere('text', 'like', "%{$keyword}%");
        }
        $prop = 'id';
        $order = 'desc';
        if (isset($sort['prop'])) {
            $prop = Str::snake($sort['prop']);
        }
        if (isset($sort['order']) && $sort['order'] === 'ascending') {
            $order = 'asc';
        }
        
        $resp = $query->orderBy($prop, $order)
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
    
    public function switchHidden(int $id, int $hidden): bool
    {
        $exists = Message::whereId($id)->first();
        if (!$exists) {
            throw new CommonException(ErrorCode::RECORD_EXCEPTION);
        }
        
        $hidden = $hidden > 0 ? 1 : 0;
        
        if ($exists->hidden !== $hidden) {
            $data['hidden'] = $hidden;
            $data['updated_at'] = date('Y-m-d H:i:s');
            foreach ($data as $key => $val) {
                $exists->setAttribute($key, $val);
            }
            $exists->save();
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_ROLE,
                $id,
                $this->uid,
                '切换隐藏',
                $data
            );
        }
        return true;
    }
    
    public function switchTop(int $id, int $top): bool
    {
        $exists = Message::whereId($id)->first();
        if (!$exists) {
            throw new CommonException(ErrorCode::RECORD_EXCEPTION);
        }
        $top = $top > 0 ? 1 : 0;
        
        if ($exists->top !== $top) {
            $data['top'] = $top;
            $data['updated_at'] = date('Y-m-d H:i:s');
            foreach ($data as $key => $val) {
                $exists->setAttribute($key, $val);
            }
            $exists->save();
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_MSG,
                $id,
                $this->uid,
                '切换置顶',
                $data
            );
        }
        return true;
    }
    
}
