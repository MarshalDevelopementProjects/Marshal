<?php

require __DIR__ . "/../vendor/autoload.php";

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin, IMAGE_TYPE, IMAGE_NAME, FILE_NAME, FILE_TYPE');
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
$router->post('/groupleader/task', GroupLeaderController::class . '::createTask');
$router->get('/groupleader/group', GroupLeaderController::class. '::getGroupInfo');
$router->post('/groupleader/announcement', GroupLeaderController::class. '::addAnnouncement');
$router->get('/groupmember/announcement', GroupMemberController::class. '::getGroupAnnouncements');
$router->get('/groupmember/group', GroupMemberController::class . '::getGroupInfo');

$router->get('/groupmember/dashboard', GroupMemberController::class . '::defaultAction');

$router->get('/projectleader/forum', ProjectLeaderController::class . '::getForum');
$router->get('/projectmember/forum', ProjectMemberController::class . '::getForum');
$router->post('/projectmember/taskfeedback', ProjectMemberController::class. '::sendTaskFeedback');
$router->get('/projectmember/taskfeedback', ProjectMemberController::class. '::getTaskFeedback');
$router->post('/groupmember/taskfeedback', GroupMemberController::class. '::sendTaskFeedback');
$router->get('/groupmember/taskfeedback', GroupMemberController::class. '::getTaskFeedback');
$router->get('/projectmember/group', ProjectMemberController::class . '::goToGroup');

$router->post('/projectmember/pickuptask', ProjectMemberController::class . '::pickupTask');
$router->post('/projectmember/sendconfirmation', ProjectMemberController::class . '::sendConfirmation');
$router->post('/projectleader/rearangetask', ProjectLeaderController::class . '::rearangeTask');
$router->post('/projectleader/assigntask', ProjectLeaderController::class . '::assignTask');
$router->post('/projectleader/announcement', ProjectLeaderController::class . '::addAnnouncement');
$router->post('/projectleader/clientfeedback', ProjectLeaderController::class . '::sendMessageToClient');
$router->get('/projectmember/announcement', ProjectMemberController::class . '::getProjectAnnouncements');

$router->post('/user/logout', UserAuthController::class . '::logout');
$router->post('/admin/logout', AdminAuthController::class . '::logout');
$router->post('/admin/users/addnewuser', AdminController::class . '::createNewUser');
$router->put('/admin/users/userblock', AdminController::class . '::blockUser');
$router->put('/admin/users/userunblock', AdminController::class . '::grantAccessToUser');
$router->get('/admin/users/all', AdminController::class . '::viewAllUsers');
$router->get('/admin/users/active', AdminController::class . '::viewActiveUsers');
$router->get('/admin/users/blocked', AdminController::class . '::viewBlockedUsers');
$router->get('/admin/users/offline', AdminController::class . '::viewOfflineUsers');

$router->get('/user/projects', UserController::class . '::viewProjects');
$router->post('/user/projects', UserController::class . '::createProject');

$router->get('/user/project', UserController::class . '::gotoProject');
$router->get('/user/notifications', UserController::class . '::getNotifications');
$router->get('/user/join', UserController::class . '::userJoinOnProject');
$router->get('/user/clicknotification', UserController::class . '::clickOnNotification');
$router->get('/user/sketch', UserController::class . '::sketch');

$router->get('/projectleader/getinfo', ProjectLeaderController::class . '::getProjectInfo');
$router->get('/projectmember/getinfo', ProjectMemberController::class . '::getProjectInfo');
$router->post('/projectleader/invite', ProjectLeaderController::class . '::sendProjectInvitation');
$router->post('/group/leader/invite', GroupLeaderController::class . '::sendGroupInvitation');
$router->post('/projectleader/createtask', ProjectLeaderController::class . '::createTask');
$router->post('/projectleader/group', ProjectLeaderController::class . '::createGroup');
$router->get('/projectmember/fileupload', ProjectMemberController::class . '::getFileUploadPage');
$router->post('/projectmember/fileupload', ProjectMemberController::class . '::fileUpload');

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

$router->get('/project/leader/project/forum/messages', ProjectLeaderController::class . '::getProjectForumMessages');
$router->post('/project/leader/project/forum/messages', ProjectLeaderController::class . '::postMessageToProjectForum');
$router->get('/project/leader/project/feedback/messages', ProjectLeaderController::class . '::getProjectFeedbackMessages');
$router->post('/project/leader/project/feedback/messages', ProjectLeaderController::class . '::postMessageToProjectFeedback');
$router->get('/project/leader/group/feedback/messages', ProjectLeaderController::class . '::getGroupFeedbackMessages');
$router->post('/project/leader/group/feedback/messages', ProjectLeaderController::class . '::postMessageToGroupFeedback');

$router->get('/project/leader/task/feedback/messages', ProjectLeaderController::class . '::getProjectTaskFeedbackMessages');
$router->post('/project/leader/task/feedback/messages', ProjectLeaderController::class . '::postMessageToProjectTaskFeedback');

$router->get('/project/member/project/forum/messages', ProjectMemberController::class . '::getProjectForumMessages');
$router->post('/project/member/project/forum/messages', ProjectMemberController::class . '::postMessageToProjectForum');
$router->get('/project/member/task/feedback/messages', ProjectMemberController::class . '::getProjectTaskFeedbackMessages');
$router->post('/project/member/task/feedback/messages', ProjectMemberController::class . '::postMessageToProjectTaskFeedback');

$router->get('/group/leader/group/forum', GroupLeaderController::class . '::getForum');

$router->get('/group/leader/group/forum/messages', GroupLeaderController::class . '::getGroupForumMessages');
$router->post('/group/leader/group/forum/messages', GroupLeaderController::class . '::postMessageToGroupForum');
$router->get('/group/leader/group/feedback/messages', GroupLeaderController::class . '::getGroupFeedbackMessages');
$router->post('/group/leader/group/feedback/messages', GroupLeaderController::class . '::postMessageToGroupFeedback');

$router->get('/group/leader/task/feedback/messages', ProjectLeaderController::class . '::getGroupTaskFeedbackMessages');
$router->post('/group/leader/task/feedback/messages', ProjectLeaderController::class . '::postMessageToGroupTaskFeedback');

$router->get('/group/member/forum', GroupMemberController::class . '::getForum');

$router->get('/group/member/forum/messages', GroupMemberController::class . '::getGroupForumMessages');
$router->post('/group/member/forum/messages', GroupMemberController::class . '::postMessageToGroupForum');

$router->get('/group/member/task/feedback/messages', GroupMemberController::class . '::getGroupTaskFeedbackMessages');
$router->post('/group/member/task/feedback/messages', GroupMemberController::class . '::postMessageToGroupTaskFeedback');

$router->get('/project/client/project/feedback/messages', ClientController::class . '::getProjectFeedbackMessages');
$router->post('/project/client/project/feedback/messages', ClientController::class . '::postMessageToProjectFeedback');
$router->get('/project/client/report', ClientController::class . '::generateProjectReport');

$router->get('/project/leader/conference/scheduler', ProjectLeaderController::class . '::gotoConferenceScheduler');
$router->get('/project/leader/conference', ProjectLeaderController::class . '::gotoConference');
$router->post('/project/leader/conference/schedule', ProjectLeaderController::class . '::scheduleConference');

$router->get('/project/client/conference/scheduler', ClientController::class . '::gotoConferenceScheduler');
$router->get('/project/client/conference', ClientController::class . '::gotoConference');
$router->post('/project/client/conference/schedule', ClientController::class . '::scheduleConference');


// sanitize the uri
$uri = htmlspecialchars(
    trim(array_key_exists("REDIRECT_URL", $_SERVER) ? $_SERVER["REDIRECT_URL"] : $_SERVER["REQUEST_URI"]),
    ENT_QUOTES,
    'UTF-8'
);

$data = Request::getData(file_get_contents('php://input'));

$router->dispatch($uri, $data);
