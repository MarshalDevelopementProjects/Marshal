<?php

namespace App\Controller\Project;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
use App\Model\Project;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectController extends UserController
{
    public function defaultAction(Object|array|string|int $optional = null)
    {
    }
}
