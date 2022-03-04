<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/1
 */

namespace App\Helpers;


use App\Services\BaseService;
use App\Services\DepartmentService;

class DingHelper extends BaseService
{
    protected string $appKey;
    protected string $appSecret;
    
    protected function __construct()
    {
        $config = config('param.ding');
        $this->appKey = $config['appKey'] ?? '';
        $this->appSecret = $config['appSecret'] ?? '';
    }
    
    protected function getToken(): string
    {
        $uri = 'https://oapi.dingtalk.com/gettoken';
        $params['appkey'] = $this->appKey;
        $params['appsecret'] = $this->appSecret;
        $resp = CurlHelper::get($uri, $params);
        $ret = json_decode($resp, true, 512, JSON_THROW_ON_ERROR);
        return $ret['access_token'] ?? '';
    }
    
    public function syncDepartment(int $deptId = 1): array
    {
        
        $token = $this->getToken();
        $uri = "https://oapi.dingtalk.com/topapi/v2/department/listsub?access_token={$token}";
        $params['dept_id'] = $deptId;
        $params['language'] = 'zh_CN';
        $resp = CurlHelper::post($uri, $params);
        $data = json_decode($resp, true, 512, JSON_THROW_ON_ERROR);
        $result = $data['result'] ?? [];
        if (empty($result)) {
            return [];
        }
        $ret = [];
        foreach ($result as $item) {
            DepartmentService::getInstance()->saveDepartmentDing($item['dept_id'], $item['parent_id'], $item['name']);
            echo $item['dept_id'] . ' ';
            echo $item['name'] . PHP_EOL;
            $tmp = $this->syncDepartment($item['dept_id']);
            $tmp && $item['children'] = $tmp;
            $ret[] = $item;
        }
        return $ret;
    }
    
    
}
