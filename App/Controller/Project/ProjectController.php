<?php

namespace App\Controller\Project;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\Controller;
use App\Model\Project;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectController extends Controller
{
    public function defaultAction(Object|array|string|int $optional = null)
    {
    }
}
