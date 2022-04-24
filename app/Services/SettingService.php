<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Models\Setting;

class SettingService extends BaseService
{
    public function getAll(): array
    {
        return Setting::whereUid($this->uid)->get()->toArray();
    }
    
    public function getOne(string $label): array
    {
        $data = Setting::whereUid($this->uid)
            ->where('label', '=', $label)
            ->first();
        return $data ? $data->toArray() : [];
    }
    
    public function save(string $label, string $value): bool
    {
        $exist = Setting::whereUid($this->uid)
            ->where('label', '=', $label)
            ->first();
        if (!$exist) {
            $data = [
                'uid' => $this->uid,
                'label' => $label,
                'value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $id = Setting::insertGetId($data);
            $data['id'] = $id;
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_SETTING,
                $id,
                $this->uid,
                '新增系统设置项',
                $data
            );
        } else {
            $data['value'] = $value;
            $data['updated_at'] = date('Y-m-d H:i:s');
            foreach ($data as $key => $val) {
                $exist->setAttribute($key, $val);
            }
            $exist->save();
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_SETTING,
                $exist->id,
                $this->uid,
                '修改系统设置',
                $data
            );
        }
        
        return true;
    }
}
