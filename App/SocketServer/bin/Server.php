<?php

namespace App\SocketServer\bin;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet;
use App\SocketServer\src\Messenger;
use App\SocketServer\src\SignalingServer;

/**
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
 * +---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
 * |                Route                                                                  |                    Route tokens                                     |
 * +---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/project/forum?project=ProjectID                                   | routing_params => ['category' => 'project', 'route' => 'forum']     |
 * |                                                                                       | http_params => ['project' => '12']                                  |
 * +---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/project/feedback?project=ProjectID                                | routing_params => ['category' => 'project', 'route' => 'feedback']  |
 * |                                                                                       | http_params => ['project' => '12']                                  |
 * +---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/groups/forum?project=ProjectID&group=GroupID                      | routing_params => ['category' => 'groups', 'route' => 'forum']      |
 * |                                                                                       | http_params => ['project' => '12', 'group' => '12']                 |
 * +---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/groups/feedback?project=ProjectID&group=GroupID                   | routing_params => ['category' => 'groups', 'route' => 'feedback']   |
 * |                                                                                       | http_params => ['project' => '12', 'group' => '12']                 |
 * +---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
 * | ws://localhost:8080/signal/projects/feedback?user=Username&project=ProjectID          | routing_params => ['category' => 'projects', 'route' => 'feedback'] |
 * |                                                                                       | http_params => ['project' => '12', 'group' => '12']                 |
 * +---------------------------------------------------------------------------------------+---------------------------------------------------------------------+
 *
 * NOTE :: THE SIGNALLING ROUTES ARE ONLY IMPLEMENTED FOR PROJECTS FEEDBACKS ONLY ALL THE OTHER ROUTES ARE DISABLED
 *
 */

$app = new Ratchet\App();
$app->route("/signal/{category}/{route}", new SignalingServer(), array('*'));
$app->route("/{category}/{route}", new Messenger(), array('*'));
$app->run();
