<?php

require __DIR__ . "/../vendor/autoload.php";

use App\Controller\Index\IndexController;
use App\Controller\User\UserController;
use App\Controller\Administrator\AdminController;
use App\Controller\Authenticate\AdminAuthController;
use App\Controller\Authenticate\UserAuthController;
use Core\Request;
use Core\Router;

// register custom error and exception handlers
// error_reporting(E_ALL);
// set_error_handler('Core\ErrorHandler::errorHandler');
// set_exception_handler('Core\ErrorHandler::exceptionHandler');

$router = new Router();

// REMEMBER +> GET methods is used to create a resource and serve it

$router->get('/', IndexController::class . '::defaultAction');

$router->get('/user/login', UserAuthController::class . '::onUserLogin');
$router->post('/user/login', UserAuthController::class . '::onUserLogin');

$router->get('/user/signup', UserAuthController::class . '::onUserSignup');
$router->post('/user/signup', UserAuthController::class . '::onUserSignup');

$router->get('/admin/login', AdminAuthController::class . '::onAdminLogin');
$router->post('/admin/login', AdminAuthController::class . '::onAdminLogin');

$router->get('/user/dashboard', UserController::class . '::defaultAction');
$router->get('/admin/dashboard', AdminController::class . '::defaultAction');

$router->post('/user/logout', UserAuthController::class . '::logout');
$router->post('/admin/logout', AdminAuthController::class . '::logout');

$router->get('/user/projects', UserController::class . '::viewProjects');
$router->post('/user/projects', UserController::class . '::createProject');

// sanitize the uri
$uri = htmlspecialchars(
    trim($_SERVER["REQUEST_URI"]),
    ENT_QUOTES,
    'UTF-8'
);

$data = Request::getData(file_get_contents('php://input'));

$router->dispatch($uri, $data);
