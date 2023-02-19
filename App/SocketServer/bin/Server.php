<?php

namespace App\SocketServer\bin;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet;
use App\SocketServer\src\Messenger;
use App\SocketServer\src\Notifier;

$app = new Ratchet\App();
$app->route("/{category}", new Messenger(), array('*'));
$app->route("/notifier", new Notifier(), array('*'));
$app->run();

