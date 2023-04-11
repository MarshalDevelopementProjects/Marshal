<?php

namespace App\SocketServer\bin;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet;
use App\SocketServer\src\Messenger;

/*
 *
 * WS Routes
 *
 * +=========+
 * |Structure|
 * +=========+
 *
 *  Projects
 * =========
 *  Base +> ws://localhost:8080/projects/
 *
 *  Project message forum +>  ws://localhost:8080/project/forum?project=ProjectID
 *
 *  Project feedback forum +>  ws://localhost:8080/project/feedback?project=ProjectID
 *
 *  Groups
 * =========
 *  Base +> ws://localhost:8080/groups/
 *
 *  Group message forum +>  ws://localhost:8080/groups/forum?project=ProjectID&group=GroupID
 *
 *  Group feedback forum +>  ws://localhost:8080/groups/feedback?project=ProjectID&group=GroupID
 *
 * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
 * |                Route                                                  |                    Route tokens                                     |
 * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/project/forum?project=ProjectID                   | routing_params => ['category' => 'project', 'route' => 'forum']     |
 * |                                                                       | http_params => ['project' => '12']                                  |
 * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/project/feedback?project=ProjectID                | routing_params => ['category' => 'project', 'route' => 'feedback']  |
 * |                                                                       | http_params => ['project' => '12']                                  |
 * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/groups/forum?project=ProjectID&group=GroupID      | routing_params => ['category' => 'groups', 'route' => 'forum']      |
 * |                                                                       | http_params => ['project' => '12', 'group' => '12']                 |
 * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/groups/feedback?project=ProjectID&group=GroupID   | routing_params => ['category' => 'groups', 'route' => 'feedback']   |
 * |                                                                       | http_params => ['project' => '12', 'group' => '12']                 |
 * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
 *  */

$app = new Ratchet\App();
$app->route("/{category}/{route}", new Messenger(), array('*'));
$app->run();
