<?php

namespace App\Controller\GroupMember;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Model\GroupMember;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class GroupMemberController extends ProjectMemberController
{
    private GroupMember $groupMember;

    public function __construct()
    {
        try {
            parent::__construct();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function defaultAction(Object|array|string|int $optional = null)
    {
    }

    public function auth()
    {
        return parent::auth();
    }
}
