<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/10
 */

return [
    'oss' => [
        'accessKeyId' => env('OSS_ACCESS_ID', ''),
        'accessKeySecret' => env('OSS_ACCESS_KEY', ''),
        'endpoint' => env('OSS_ENDPOINT', ''),
        'bucket' => env('OSS_BUCKET', ''),
        'cdnDomain' => env('OSS_CDN_DOMAIN', ''),
    ],
    'sendCode' => [
        'accessKeyId' => env('SMS_ACCESS_ID', ''),
        'accessKeySecret' => env('SMS_ACCESS_KEY', ''),
        'regionId' => env('SMS_REGION_ID', ''),
        'sign' => 'ICY',
        'templateCode' => 'SMS_210765060',
        'templateName' => '通用验证码',
    ]
];
