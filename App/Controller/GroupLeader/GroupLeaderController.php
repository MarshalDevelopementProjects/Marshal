<?php

namespace App\Controller\GroupLeader;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Model\GroupLeader;

require __DIR__ . '/../../../vendor/autoload.php';

class GroupLeaderController extends ProjectMemberController
{
    private GroupLeader $groupLeader;

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

    public function auth(): bool
    {
        return parent::auth();
    }

    public function serverMessageForum(array $args)
    {
    }
}
