<?php

namespace App\Controller\Index;

require __DIR__ . "/../../../vendor/autoload.php";

use App\Controller\Authenticate\UserAuthController;

class IndexController extends UserAuthController
{

  public function __construct()
  {
    parent::__construct();
  }

  public function defaultAction(Object|array|string|int $optional = null): void
  {
    if (parent::isLogged()) {
      if ((parent::getCredentials()->primary_role == "admin")) {
        header("Location: http://localhost/public/admin/dashboard");
      } else {
        header("Location: http://localhost/public/user/dashboard");
      }
    } else
      $this->sendResponse(view: "/landing.html", status: "success");
    exit;
  }
}
