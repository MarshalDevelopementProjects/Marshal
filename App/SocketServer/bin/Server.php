<?php

namespace App\SocketServer\bin;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet;
use App\SocketServer\src\MessageController;

$app = new Ratchet\App();
$app->route("/{category}", new MessageController(), array('*'));
$app->run();

