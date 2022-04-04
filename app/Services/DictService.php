<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Models\Dict;
use App\Models\DictData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;

class DictService extends BaseService
{
    public function getList(
        string $dictName,
        string $symbol,
        string $remark,
        array $createdAt,
        array $sort = [],
        int $page = 1,
        int $pageSize = 30
    ): array
    {
        $ret = [
            'cnt' => 0,
            'list' => [],
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        $query = Dict::select([
            'id',
            'dict_name',
            'symbol',
            'remark',
            'created_at',
            'updated_at',
        ]);
        $dictName && $query->where('dict_name', 'like', "%{$dictName}%");
        $symbol && $query->where('symbol', 'like', "%{$symbol}%");
        $remark && $query->where('remark', 'like', "%{$remark}%");
        if ($createdAt) {
            $start = date('Y-m-d 00:00:00', strtotime($createdAt[0]));
            $end = date('Y-m-d 23:59:59', strtotime($createdAt[1]));
            $query->whereBetween('created_at', [$start, $end]);
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
        if (empty($resp)) {
            return $ret;
        }
        
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
    
    public function save(
        string $dictName,
        string $symbol,
        string $remark,
        int $id = 0
    ): array
    {
        $ret = [];
        $exists = Dict::whereSymbol($symbol)
            ->where('id', '!=', $id)
            ->first();
        if ($exists) {
            throw new CommonException(ErrorCode::RECORD_EXISTS);
        }
        DB::beginTransaction();
        try {
            if ($id > 0) {
                $exist = Dict::whereId($id)->first();
                if ($exist) {
                    $updateData = [];
                    $dictName !== $exist->dict_name && $updateData['dict_name'] = $dictName;
                    $symbol !== $exist->symbol && $updateData['symbol'] = $symbol;
                    $remark !== $exist->remark && $updateData['remark'] = $remark;
                    if ($updateData) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        foreach ($updateData as $key => $val) {
                            $exist->setAttribute($key, $val);
                        }
                        $exist->save();
                        ActionLogService::getInstance()->insert(
                            ActionLogService::RESOURCE_DICT,
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
                    'dict_name' => $dictName,
                    'symbol' => $symbol,
                    'remark' => $remark,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $id = Dict::insertGetId($data);
                $data['id'] = $id;
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_DICT,
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
    
    public function saveData(
        int $dictId,
        int $sort,
        string $label,
        string $value,
        string $remark,
        int $id = 0
    ): array
    {
        $ret = [];
        $exists = DictData::whereDictId($dictId)
            ->where('value', '=', $value)
            ->where('id', '!=', $id)
            ->first();
        if ($exists) {
            throw new CommonException(ErrorCode::RECORD_EXISTS);
        }
        DB::beginTransaction();
        try {
            if ($id > 0) {
                $exist = DictData::whereId($id)->first();
                if ($exist) {
                    $updateData = [];
                    $sort !== $exist->sort && $updateData['sort'] = $sort;
                    $label !== $exist->label && $updateData['label'] = $label;
                    $value !== $exist->value && $updateData['value'] = $value;
                    $remark !== $exist->remark && $updateData['remark'] = $remark;
                    if ($updateData) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        foreach ($updateData as $key => $val) {
                            $exist->setAttribute($key, $val);
                        }
                        $exist->save();
                        $updateData['id'] = $id;
                        ActionLogService::getInstance()->insert(
                            ActionLogService::RESOURCE_DICT,
                            $dictId,
                            $this->uid,
                            '编辑数据',
                            $updateData
                        );
                    }
                }
                $data = $exist->toArray();
            } else {
                $data = [
                    'dict_id' => $dictId,
                    'sort' => $sort,
                    'label' => $label,
                    'value' => $value,
                    'remark' => $remark,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $id = DictData::insertGetId($data);
                $data['id'] = $id;
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_DICT,
                    $dictId,
                    $this->uid,
                    '新增数据',
                    $data
                );
            }
            $data['created_at'] = formatDateTime($data['created_at']);
            $data['updated_at'] = formatDateTime($data['updated_at']);
            foreach ($data as $key => $v) {
                $ret[Str::camel($key)] = $v;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $ret;
    }
    
    public function switchDataStatus(int $dataId, int $dictStatus): bool
    {
        $exists = DictData::whereId($dataId)->first();
        if (!$exists) {
            throw new CommonException(ErrorCode::RECORD_EXCEPTION);
        }
        
        $statusMap = $this->getDictDataBySymbol('DictDataType', '1');
        $statusMap = array_column($statusMap, 'label', 'value');
        if (!array_key_exists($dictStatus, $statusMap)) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT);
        }
        
        if ($exists->data_status !== $dictStatus) {
            DB::beginTransaction();
            try {
                $data['data_status'] = $dictStatus;
                $data['updated_at'] = date('Y-m-d H:i:s');
                foreach ($data as $key => $val) {
                    $exists->setAttribute($key, $val);
                }
                $exists->save();
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_DICT,
                    $exists->dict_id,
                    $this->uid,
                    '切换状态',
                    ['id' => $dataId]
                );
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        return true;
    }
    
    public function getDictDataBySymbol(string $symbol, string $dataStatus = ''): array
    {
        $dictTb = (new Dict())->getTable();
        $dictDataTb = (new DictData())->getTable();
        $query = DB::table("{$dictDataTb} as dd")
            ->leftJoin("{$dictTb} as d", 'd.id', '=', 'dd.dict_id')
            ->select([
                'dd.*',
            ])
            ->where('d.symbol', '=', $symbol);
        $dataStatus !== '' && $query->where('dd.data_status', '=', $dataStatus);
        $data = $query->orderBy('dd.sort', 'asc')->get()->toArray();
        $ret = [];
        foreach ($data as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $tmp = [];
            foreach ($item as $key => $value) {
                $tmp[Str::camel($key)] = $value;
            }
            $ret[] = $tmp;
        }
        return $ret;
    }
    
    public function remove(int $id): bool
    {
        $ret = false;
        $exist = Dict::whereId($id)->first();
        if ($exist) {
            DB::beginTransaction();
            try {
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_DICT,
                    $id,
                    $this->uid,
                    '删除',
                    $exist->toArray()
                );
                $ret = $exist->delete();
                DB::commit();
                $this->removeData($id);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        return $ret;
    }
    
    public function removeData(int $dictId, $id = 0): bool
    {
        $ret = false;
        $data = DictData::whereDictId($dictId)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', '=', $id);
            })
            ->get()
            ->toArray();
        if ($data) {
            DB::beginTransaction();
            try {
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_DICT,
                    $dictId,
                    $this->uid,
                    '删除数据',
                    $data
                );
                $ret = DictData::whereDictId($dictId)
                    ->when($id, function ($query) use ($id) {
                        return $query->where('id', '=', $id);
                    })
                    ->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        }
        return $ret;
    }
}
