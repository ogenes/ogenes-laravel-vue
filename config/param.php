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

return [
    'ding' => $ding,
    'alioss' => $alioss,
];
