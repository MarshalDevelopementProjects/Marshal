<?php

namespace App\Controller\ProjectMember;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
use App\Model\ProjectMember;
use App\Model\Project;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectMemberController extends UserController
{
    private ProjectMember $projectMember;

    public function __construct()
    {
        try {
            parent::__construct();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function defaultAction(Object|array|string|int $data = null)
    {
    }

    public function auth()
    {
        return parent::auth();
    }
}
