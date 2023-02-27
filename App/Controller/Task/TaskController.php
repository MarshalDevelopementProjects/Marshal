<?php

namespace App\Controller\Task;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\Controller;
use App\Model\Task;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class TaskController extends Controller
{
    public function defaultAction(Object|array|string|int $optional = null)
    {
    }
}
