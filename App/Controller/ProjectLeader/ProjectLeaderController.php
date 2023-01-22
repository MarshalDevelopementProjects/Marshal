<?php

namespace App\Controller\ProjectLeader;

use App\Controller\User\UserController;
use App\Model\ProjectLeader;
use App\Model\Project;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectLeaderController extends UserController
{
    private ProjectLeader $projectLeader;
    private Project $project;

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

    // in here check the user role whether it is project leader regarding the project
    public function auth()
    {
        return parent::auth();
    }
}
