<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/5/5
 */

namespace App\Helpers;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use App\Models\SmsRecord;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsHelper
{
    
    public const
        SMS_PREFIX = 'OG:SEND:CODE:',
        SMS_EXPIRE = 600;
    
    public function sendCode(string $phoneNumbers): bool
    {
        $baseService = AuthService::getInstance();
        $code = getRandStr(4, ['number']);
        $key = self::SMS_PREFIX . $phoneNumbers;
        $data = [
            'code' => $code,
            'userId' => $baseService->uid,
            'time' => time()
        ];
        $baseService->getRedis()->set(
            $key, json_encode($data,
            JSON_THROW_ON_ERROR),
            self::SMS_EXPIRE);
        
        $signName = config('param.alisms.sign');
        $templateCode = config('param.alisms.templateCode');
        $templateParam = [
            'code' => $code
        ];
        
        $record = New SmsRecord();
        $record->uid = $baseService->uid;
        $record->mobile = $phoneNumbers;
        $record->sign_name = $signName;
        $record->template_code = $templateCode;
        $record->template_param = json_encode($templateParam, JSON_THROW_ON_ERROR);
        $record->created_at = date('Y-m-d H:i:s');
        $record->result = -1;
        $record->reason = '';
        $record->save();
        $this->send($record);
        return true;
    }
    
    public function checkCode(string $phoneNumbers, string $code): bool
    {
        $key = self::SMS_PREFIX . $phoneNumbers;
        $json = AuthService::getInstance()->getRedis()->get($key);
        if ($json) {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $sendTime = $data['time'] ?? 0;
            if ($sendTime + self::SMS_EXPIRE >= time()) {
                $sendCode = $data['code'] ?? '12345';
                if ($sendCode === $code) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function send(SmsRecord $record): bool
    {
        $accessKeyId = config('param.alisms.accessKeyId');
        $accessKeySecret = config('param.alisms.accessKeySecret');
        $regionId = config('param.alisms.regionId');
        try {
            $record->send_at = date('Y-m-d H:i:s');
            AlibabaCloud::accessKeyClient($accessKeyId, $accessKeySecret)
                ->regionId($regionId)
                ->asDefaultClient();
            $resp = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'PhoneNumbers' => $record->mobile,
                        'SignName' => $record->sign_name,
                        'TemplateCode' => $record->template_code,
                        'TemplateParam' => $record->template_param,
                    ],
                ])
                ->request();
            $ret = $resp->toArray();
            
            if (isset($ret['Code']) && $ret['Code'] === 'OK') {
                $record->result = 1;
                $record->save();
                return true;
            }
            $record->result = 0;
            $record->reason = $ret['Message'] ?? '';
            $record->save();
            throw new RuntimeException($record->reason);
        } catch (ClientException $e) {
            Log::error(__METHOD__, [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'msg' => $e->getMessage()
            ]);
            throw new RuntimeException($e->getMessage());
        } catch (ServerException $e) {
            Log::error(__METHOD__, [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'msg' => $e->getMessage()
            ]);
            throw new RuntimeException($e->getMessage());
        }
    }
}
