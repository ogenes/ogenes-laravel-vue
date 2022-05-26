<?php
/**
 * User: john <john.yi@55haitao.com>
 * Date: 2022/5/24
 */

namespace App\Http\Controllers;

use SwooleTW\Http\Websocket\Websocket;
use SwooleTW\Http\Websocket\Facades\Websocket as WebsocketFacades;

class WebsocketController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function notify(Websocket $websocket, $data): void
    {
        $uid = $websocket->getUserId();
        if ($uid <= 0) {
            return;
        }
        if ($data['type'] === 'read') {
            $ret['type'] = 'decr';
            echo "{$uid} - decr" . PHP_EOL;
            WebsocketFacades::toUserId($uid)->emit('notify-refresh', json_encode($ret, JSON_THROW_ON_ERROR));
        }
        
        if ($data['type'] === 'add') {
            $ret['type'] = 'incr';
            echo "all user - incr" . PHP_EOL;
            WebsocketFacades::broadcast()->emit('notify-refresh', json_encode($ret, JSON_THROW_ON_ERROR));
        }
    
    }
    
}
