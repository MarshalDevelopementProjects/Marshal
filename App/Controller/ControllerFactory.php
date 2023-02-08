<?php

namespace App\Controller;

require __DIR__ . '/../../vendor/autoload.php';

use App\Controller\Controller;
use App\Controller\Administrator\AdminController;
use App\Controller\ProjectLeader\ProjectLeaderController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Controller\GroupLeader\GroupLeaderController;
use App\Controller\GroupMember\GroupMemberController;

class ControllerFactory
{
    public static function factory(string $controller): ?Controller
    {
        return match ($controller) {
            "Admin" => new AdminController(),
            "ProjectLeader" => new ProjectLeaderController(),
            "ProjectMember" => new ProjectMemberController(),
            "GroupLeader" => new GroupLeaderController(),
            "GroupMember" => new GroupMemberController(),
            default => null,
        };
    }
}