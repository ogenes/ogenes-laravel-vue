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

return [
    'ding' => $ding
];
