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
        $host = '127.0.0.1';
        $port = '8888';
        $uri = "ws://{$host}:{$port}?Authorization={$token}";
        $options = array_merge([
            'uri' => $uri,
            'opcode' => 'text',
        ]);
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
