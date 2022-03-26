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
        $resp = $query->orderBy('id', 'desc')
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
        $exists = Dict::whereSymbol($symbol)
            ->where('id', '!=', $id)
            ->first();
        if ($exists) {
            throw new CommonException(ErrorCode::RECORD_EXISTS);
        }
        if ($id > 0) {
            $exist = Dict::whereId($id)->first();
            if ($exist) {
                $exist->setAttribute('dict_name', $dictName);
                $exist->setAttribute('symbol', $symbol);
                $exist->setAttribute('remark', $remark);
                $exist->setAttribute('updated_at', date('Y-m-d H:i:s'));
                $exist->save();
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
            $data['id'] = Dict::insertGetId($data);
        }
        $ret = [];
        $data['created_at'] = formatDateTime($data['created_at']);
        $data['updated_at'] = formatDateTime($data['updated_at']);
        foreach ($data as $key => $value) {
            $ret[Str::camel($key)] = $value;
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
        $exists = DictData::whereDictId($dictId)
            ->where('value', '=', $value)
            ->where('id', '!=', $id)
            ->first();
        if ($exists) {
            throw new CommonException(ErrorCode::RECORD_EXISTS);
        }
        if ($id > 0) {
            $exist = DictData::whereId($id)->first();
            if ($exist) {
                $exist->setAttribute('sort', $sort);
                $exist->setAttribute('label', $label);
                $exist->setAttribute('value', $value);
                $exist->setAttribute('remark', $remark);
                $exist->setAttribute('updated_at', date('Y-m-d H:i:s'));
                $exist->save();
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
            $data['id'] = DictData::insertGetId($data);
        }
        $ret = [];
        $data['created_at'] = formatDateTime($data['created_at']);
        $data['updated_at'] = formatDateTime($data['updated_at']);
        foreach ($data as $key => $v) {
            $ret[Str::camel($key)] = $v;
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
            $exists->data_status = $dictStatus;
            $exists->save();
            //record log
        }
        return true;
    }
    
    public function getDictDataBySymbol(string $symbol, string $dataStatus = ''): array
    {
        $dictTb = (new Dict())->getTable();
        $dictDataTb = (new DictData())->getTable();
        $query = DB::table("{$dictTb} as d")
            ->leftJoin("{$dictDataTb} as dd", 'd.id', '=', 'dd.dict_id')
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
}
