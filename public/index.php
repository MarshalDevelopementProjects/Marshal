<?php

require __DIR__ . "/../vendor/autoload.php";

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin, IMAGE_TYPE, IMAGE_NAME');
header('Access-Control-Allow-Methods:  POST, PUT, GET, DELETE');

use Core\Request;
use Core\Router;
use App\Controller\Index\IndexController;
use App\Controller\User\UserController;
use App\Controller\Administrator\AdminController;
use App\Controller\Authenticate\AdminAuthController;
use App\Controller\Authenticate\UserAuthController;
use App\Controller\Client\ClientController;
use App\Controller\GroupLeader\GroupLeaderController;
use App\Controller\GroupMember\GroupMemberController;
use App\Controller\ProjectLeader\ProjectLeaderController;
use App\Controller\ProjectMember\ProjectMemberController;

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
$router->get('/client/dashboard', ClientController::class . '::defaultAction');

$router->get('/projectleader/dashboard', ProjectLeaderController::class . '::defaultAction');
$router->get('/projectmember/dashboard', ProjectMemberController::class . '::defaultAction');
$router->get('/groupleader/dashboard', GroupLeaderController::class . '::defaultAction');
$router->get('/groupmember/dashboard', GroupMemberController::class . '::defaultAction');

$router->post('/user/logout', UserAuthController::class . '::logout');
$router->post('/admin/logout', AdminAuthController::class . '::logout');

$router->get('/user/projects', UserController::class . '::viewProjects');
$router->post('/user/projects', UserController::class . '::createProject');

$router->get('/user/project', UserController::class . '::gotoProject');
$router->get('/user/notifications', UserController::class . '::getNotifications');
$router->get('/user/join', UserController::class . '::userJoinOnProject');
$router->get('/user/clicknotification', UserController::class . '::clickOnNotification');

$router->get('/projectleader/getinfo', ProjectLeaderController::class . '::getProjectInfo');
$router->post('/projectleader/invite', ProjectLeaderController::class . '::sendProjectInvitation');
$router->post('/projectleader/createtask', ProjectLeaderController::class . '::createTask');

$router->get('/user/profile', UserController::class . '::viewProfile');
$router->put('/user/profile/edit', UserController::class . '::editProfile');
$router->post('/user/profile/edit/picture', UserController::class . '::uploadProfilePicture');

$router->get('/user/signup/email/verification', UserAuthController::class . '::verifyUserEmailOnSignUp');
$router->post('/user/edit/password', UserController::class . '::changePassword');
$router->put('/user/edit/password', UserController::class . '::changePassword');

$router->get('/user/forgot/password', UserAuthController::class . '::forgotPasswordServePage');
$router->post('/user/forgot/password/verification', UserAuthController::class . '::sendVerificationOnForgotPassword');
$router->post('/user/forgot/password/verify', UserAuthController::class . '::verifyCodeOnForgotPassword');
$router->put('/user/forgot/password/update', UserAuthController::class . '::updateUserPasswordOnForgotPassword');

$router->get('/project/forum', UserController::class . "::gotoForum"); // check the user role and give the correct page
// $router->post('/project/forum', UserController::class . "::"); // for the post request to add the message to the database

// sanitize the uri
$uri = htmlspecialchars(
    trim(array_key_exists("REDIRECT_URL", $_SERVER) ? $_SERVER["REDIRECT_URL"] : $_SERVER["REQUEST_URI"]),
    ENT_QUOTES,
    'UTF-8'
);

$data = Request::getData(file_get_contents('php://input'));

try {
    $router->dispatch($uri, $data);
} catch (Exception $exception) {
}
