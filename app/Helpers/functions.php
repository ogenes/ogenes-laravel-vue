<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/9
 */

namespace App\Helpers;


use Godruoyi\Snowflake\Snowflake;
use Illuminate\Http\Request;

function getRealIp(): string
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $IP = getenv('HTTP_CLIENT_IP');
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $IP = $_SERVER['REMOTE_ADDR'];
    } elseif ($_SERVER['HTTP_VIA']) {
        $IP = $_SERVER['HTTP_VIA'];
    } else {
        $IP = '';
    }
    return trim(substr($IP, strpos($IP, " ")));
}

function getUniqId(int $workerId = 1): string
{
    $machineId = env('MACHINE_ID', 1);
    $snowFlake = new Snowflake($machineId, $workerId);
    return $snowFlake->id();
}

function getRandStr(int $len, array $conf = ['number', 'upper', 'lower']): string
{
    $number = '1234567890';
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $special = "~!@#$%^&*()[{]}-_=+|;:'\",<.>/?`";
    $match = '';
    in_array('number', $conf, true) && $match .= $number;
    in_array('upper', $conf, true) && $match .= $upper;
    in_array('lower', $conf, true) && $match .= $lower;
    in_array('special', $conf, true) && $match .= $special;
    
    $match = str_shuffle($match);
    return substr($match, 0, $len);
}

function formatDateTime(?string $dateTime): string
{
    if (empty($dateTime)) {
        return '';
    }
    $ret = date('Y-m-d H:i:s', strtotime($dateTime));
    if ($ret === '2000-01-01 00:00:01') {
        $ret = '/';
    }
    return $ret;
}

function getParams($request): array
{
    if (!isset($request['data'])) {
        return [];
    }
    return is_array($request['data']) ? $request['data'] : json_decode($request['data'], true, 512, JSON_THROW_ON_ERROR);
}

function filterTree(array &$treeData, array $ids):void {
    foreach ($treeData as $key => $item) {
        
        if (!in_array($item['id'], $ids, false)) {
            unset($treeData[$key]);
        }
        if (isset($treeData[$key]['children']) && $treeData[$key]['children']) {
            filterTree($treeData[$key]['children'], $ids);
        }
    }
    $treeData = array_values($treeData);
}
