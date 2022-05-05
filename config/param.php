<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/2
 */

$ding = [
    'appKey' => env('DING_APP_KEY', ''),
    'appSecret' => env('DING_APP_SECRET', ''),
];

$alioss = [
    'accessKeyId' => env('OSS_ACCESS_ID', ''),
    'accessKeySecret' => env('OSS_ACCESS_KEY', ''),
    'endpoint' => env('OSS_ENDPOINT', ''),
    'bucket' => env('OSS_BUCKET', ''),
    'cdnDomain' => env('OSS_CDN_DOMAIN', ''),
];

$alisms = [
    'accessKeyId' => env('SMS_ACCESS_ID', ''),
    'accessKeySecret' => env('SMS_ACCESS_KEY', ''),
    'regionId' => env('SMS_REGION_ID', ''),
    'sign' => 'ICY',
    'templateCode' => 'SMS_210765060',
    'templateName' => '通用验证码',
];

return [
    'ding' => $ding,
    'alioss' => $alioss,
    'alisms' => $alisms,
];
