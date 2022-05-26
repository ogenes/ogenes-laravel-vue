<?php


use App\Http\Controllers\WebsocketController;
use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;
use App\Http\Middleware\Swoole\AuthMiddleware;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/

Websocket::on('connect', function ($websocket) {
    $uid = $websocket->getUserId() ? : 0;
    echo $uid . ':connect'. PHP_EOL;
})->middleware(AuthMiddleware::class);

Websocket::on('disconnect', function ($websocket) {
    $websocket->logout();
    $uid = $websocket->getUserId() ? : 0;
    echo "{$uid}:disconnect" . PHP_EOL;
});

Websocket::on('ping', function ($websocket) {
    $websocket->emit('pong', '');
    
});

Websocket::on('notify', [WebsocketController::class, 'notify']);
