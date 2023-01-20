<?php

namespace App\Controller\Index;

require __DIR__ . "/../../../vendor/autoload.php";

use App\Controller\Controller;

class IndexController extends Controller
{
    public function defaultAction()
    {
      $this->sendResponse(view: "/landing.html", status: "success"); 
    }
}
