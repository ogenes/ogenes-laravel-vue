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
    public function notify(Websocket $websocket, $data)
    {
        echo 'UID' . $websocket->getUserId() . PHP_EOL;
        WebsocketFacades::toUserId([1])->emit('message', 'hi there');
    }
    
}
