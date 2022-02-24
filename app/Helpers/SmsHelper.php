<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/10
 */

namespace App\Helpers;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Sms\Sms;
use App\Models\SmsRecord;
use App\Services\User\AuthService;
use App\Services\User\UserService;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class SmsHelper
{
    /**
     * @var array
     */
    protected static array $instance = [];

    /**
     * @return SmsHelper
     */
    public static function getInstance(): SmsHelper
    {
        $className = static::class;
        if (isset(self::$instance[$className])) {
            return self::$instance[$className];
        }
        self::$instance[$className] = new self();
        return self::$instance[$className];
    }

    public const
        SMS_PREFIX = 'CYNIC:SEND:CODE:',
        SMS_EXPIRE = 600;

    public function sendCode(string $phoneNumbers): bool
    {
        $code = getRandStr(4, ['number']);
        $key = self::SMS_PREFIX . $phoneNumbers;
        $data = [
            'code' => $code,
            'userId' => AuthService::getInstance()->userId,
            'time' => time()
        ];
        AuthService::getInstance()->getRedis()->set(
            $key, json_encode($data,
            JSON_THROW_ON_ERROR),
            self::SMS_EXPIRE);

        $signName = config('aliyun.sendCode.sign');
        $templateCode = config('aliyun.sendCode.templateCode');
        $templateParam = [
            'code' => $code
        ];

        $record = New SmsRecord();
        $record->uid = AuthService::getInstance()->userId;
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
        $accessKeyId = config('aliyun.sendCode.accessKeyId');
        $accessKeySecret = config('aliyun.sendCode.accessKeySecret');
        $regionId = config('aliyun.sendCode.regionId');
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
