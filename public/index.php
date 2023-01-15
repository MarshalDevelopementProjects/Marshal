<?php

require __DIR__ . "/../vendor/autoload.php";

use App\Controller\Authenticate\AuthenticateController;
use App\Controller\Index\IndexController;
use App\Controller\User\UserController;
use App\Controller\Administrator\AdministratorController;
use Core\Request;
use Core\Response;
use Core\Router;

// register custom error and exception handlers
// error_reporting(E_ALL);
// set_error_handler('Core\ErrorHandler::errorHandler');
// set_exception_handler('Core\ErrorHandler::exceptionHandler');

$router = new Router();

$router->get('', IndexController::class . '::defaultAction');

$router->get('login', AuthenticateController::class . '::onLogIn');
$router->post('login', AuthenticateController::class . '::onLogIn');

$router->get('signup', AuthenticateController::class . '::OnSignUp');
$router->post('signup', AuthenticateController::class . '::OnSignUp');

$router->get('user/dashboard', UserController::class . '::defaultAction');
$router->get('admin/dashboard', AdministratorController::class . '::defaultAction');

$router->post('logout', AuthenticateController::class . '::OnLogOut');

$router->get('logged', AuthenticateController::class . '::logged');

$router->get('user/projects', UserController::class . '::viewProjects');
$router->post('user/projects', UserController::class . '::createProject');

// sanitize the uri
$uri = htmlspecialchars(
    trim($_SERVER["REQUEST_URI"]),
    ENT_QUOTES,
    'UTF-8'
);

$data = Request::getData(file_get_contents('php://input'));

$router->dispatch($uri, $data);
