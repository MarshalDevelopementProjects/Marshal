<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\SocketServer\SocketServer;

function testServerScript(): void
{
    $server = new SocketServer();
}

testServerScript();
