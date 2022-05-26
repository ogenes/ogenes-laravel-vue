<?php
/**
 * User: john <john.yi@55haitao.com>
 * Date: 2022/5/26
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use WebSocket\Client;

class WebsocketClientHelper
{
    
    public static function send(string $token, string $event, array $data)
    {
        $host = config('swoole_http.server.host');
        $port = config('swoole_http.server.port');
        $options = [
            'uri' => "ws://{$host}:{$port}?Authorization={$token}",
            'opcode' => 'text',
        ];
        try {
            $param = [
                'event' => $event,
                'data' => $data
            ];
            $client = new Client($options['uri'], $options);
            $client->send(json_encode($param, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $options['opcode']);
            $client->close();
        } catch (\Throwable $e) {
            Log::warning("Websocket Client Error", [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
            ]);
        }
        
    }
    
}
