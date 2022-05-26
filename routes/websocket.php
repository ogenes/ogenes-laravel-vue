<?php


use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;
use App\Http\Middleware\Swoole\AuthMiddleware as SwooleAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/

Websocket::on('connect', function ($websocket, Request $request) {
    // called while socket on connect
    echo 'connect' . $request->url() . PHP_EOL;
})->middleware(SwooleAuthMiddleware::class);

Websocket::on('disconnect', function ($websocket) {
    // called while socket on disconnect
    echo "disconnect" . PHP_EOL;
});

Websocket::on('ping', function ($websocket, Request $request) {
    // called while socket on connect
    $data = [];
    $websocket->emit('pong', json_encode($data, JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));
    
});

Websocket::on('notify', [\App\Http\Controllers\WebsocketController::class, 'notify']);
