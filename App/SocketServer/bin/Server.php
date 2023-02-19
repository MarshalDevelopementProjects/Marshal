<?php

namespace App\SocketServer\bin;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet;
use App\SocketServer\src\Messenger;

$app = new Ratchet\App();
$app->route("/{category}", new Messenger(), array('*'));
$app->run();
