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
    
    public function save(
        int    $id,
        string $title,
        string $banner,
        string $desc,
        string $text,
        string $releaseAt
    ): array
    {
        $ret = [];
        DB::beginTransaction();
        try {
            if ($id > 0) {
                $exist = Message::whereId($id)->first();
                if ($exist) {
                    $updateData = [];
                    $title !== $exist->title && $updateData['title'] = $title;
                    $desc !== $exist->desc && $updateData['desc'] = $desc;
                    $text !== $exist->text && $updateData['text'] = $text;
                    $banner && $banner !== $exist->banner && $updateData['banner'] = $banner;
                    $releaseAt && $releaseAt !== $exist->release_at && $updateData['release_at'] = $releaseAt;
                    if ($updateData) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        foreach ($updateData as $key => $val) {
                            $exist->setAttribute($key, $val);
                        }
                        $exist->save();
                        ActionLogService::getInstance()->insert(
                            ActionLogService::RESOURCE_MSG,
                            $id,
                            $this->uid,
                            '编辑',
                            $updateData
                        );
                    }
                }
                $data = $exist->toArray();
            } else {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'banner' => $banner,
                    'text' => $text,
                    'author_id' => $this->uid,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $releaseAt && $data['release_at'] = date('Y-m-d H:i:s', strtotime($releaseAt));
                $id = Message::insertGetId($data);
                $data['id'] = $id;
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
