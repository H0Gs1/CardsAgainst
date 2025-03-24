<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyGame\Game;

require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Game()
        )
    ),
    9090
);

$server->run();
