<?php

namespace App\Controller\Administrator;

use App\Controller\Authenticate\AdminAuthController;
use App\Controller\Controller;
use App\Model\Admin;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class AdminController extends Controller
{
    private AdminAuthController $adminAuth;
    private Admin $admin;
    private Validator $validator;

    public function __construct()
    {
        try {
            parent::__construct();
            $this->adminAuth = new AdminAuthController();
            if ($this->auth()) {
                if ($this->adminAuth->getCredentials()->primary_role == "user") {
                    $this->sendResponse(
                        view: "/errors/403.html",
                        status: "unauthorized"
                    );
                    exit;
                } else {
                    $credentials = $this->adminAuth->getCredentials();
                    $this->admin = new Admin($credentials->id);
                }
            } else {
                $this->sendResponse(
                    view: "/admin/login.html",
                    status: "unauthorized"
                );
                exit;
            }
            $this->validator = new Validator();
        } catch (\Exception $exception) {
            $this->sendJsonResponse(
                status: "unauthorized",
                content: array("message" => "No such user")
            );
            exit;
        }
    }

    public function auth()
    {
        return $this->adminAuth->isLogged();
    }

    public function defaultAction(Object|array|string|int $optional = null)
    {
        // default page should have the following 
        // user count, active user count, admin count
        // and all the users
        try {

            $data = array();
            if($this->admin->readAllUsers() == "0"){
                $data["all_user_count"] = 0;
            }else{
                $data["user_details"] = $this->admin->getQueryResults();
                $data["all_user_count"] = sizeof($data["user_details"]);
            }
            
            $data["admin_data"] = $this->admin->getAdminData();

            $count = array();
            if($this->admin->getBlockedUsers() == "0"){
                $data["block_user_count"] = 0;
            }else{
                $count["block_users"] = $this->admin->getQueryResults();
                $data["block_user_count"] = sizeof($count["block_users"]);
            }

            if($this->admin->getActiveUsers() == "0"){
                $data["active_user_count"] = 0;
            }else{
                $count["active_users"] = $this->admin->getQueryResults();
                $data["active_user_count"] = sizeof($count["active_users"]);
            }
           
            if($this->admin->getOfflineUsers() == "0"){
                $data["offline_user_count"] = 0;
            }else{
                $count["offline_users"] = $this->admin->getQueryResults();
                $data["offline_user_count"] = sizeof($count["offline_users"]);
            }

            $this->sendResponse(
                view: "/admin/dashboard.html",
                status: "success",
                content: $data
            );
            exit;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // get all the users in the database
    public function viewAllUsers()
    {
        try {
            // check whether the user exists first by checking the count then send the details
            // and unset the user password from this
            if ($this->admin->readAllUsers()) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "Users successfully retrieved",
                        "user_details" => $this->admin->getQueryResults()
                    )
                );
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array(
                        "message" => "System users cannot be retrieved",
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // this $args here should contain a field with a key as the
    // column name and the value as the column value
    public function viewUserDetails(array $args)
    {
        try {
            // check whether the user exists first by checking the count then send the details
            // and unset the user password from this
            if ($this->admin->readUser($args["key"], $args["value"])) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "User details successfully retrieved",
                        "user_details" => $this->admin->getQueryResults()
                    )
                );
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array(
                        "message" => "User details cannot be retrieved",
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createNewUser(array $args = array())
    {
        try {
            // validate the data first and then create the user
            $this->validator->validate(values: $args, schema: "admin_create_user");
            if ($this->validator->getPassed()) {
                unset($args['password_re_enter']);
                if ($this->admin->createUser($args))
                    $this->sendJsonResponse(
                        status: "success",
                        content: array(
                            "message" => "New user successfully created",
                        )
                    );
                else
                    $this->sendJsonResponse(
                        status: "internal_server_error",
                        content: array(
                            "message" => "User cannot be created",
                        )
                    );
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array(
                        "message" => "You have the errors in our inputs",
                        "errors" => $this->validator->getErrors()
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // this args array should contain the following
    // $args["key"] => "column_name"
    // $args["value"] => "column_value"
    public function blockUser(array $args)
    {
        try {
            if ($this->admin->disableUserAccount(key: $args["key"], value: $args["value"])) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "User account successfully disabled",
                    )
                );
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array(
                        "message" => "User account cannot be disabled",
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // this args array should contain the following
    // $args["key"] => "column_name"
    // $args["value"] => "column_value"
    public function grantAccessToUser(array $args)
    {
        try {
            if ($this->admin->enableUserAccount(key: $args["key"], value: $args["value"])) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "Active users successfully activated",
                    )
                );
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array(
                        "message" => "User account cannot be activated",
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function viewActiveUsers()
    {
        try {

            if ($this->admin->getActiveUsers()) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "Active users successfully retrieved",
                        "active_users" => $this->admin->getQueryResults()
                    )
                );
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array(
                        "message" => "Active users cannot be retrieved",
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function viewBlockedUsers()
    {
        try {
            if ($this->admin->getBlockedUsers()) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "Blocked users successfully retrieved",
                        "blocked_users" => $this->admin->getQueryResults()
                    )
                );
            } else {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "Blocked users cannot be retrieved",
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // method will be used to send emails to either
    // all or a single user of the system
    public function sendEmailsToUsers(array $args)
    {
    }
    public function viewOfflineUsers()
    {
        try {
            if ($this->admin->getOfflineUsers()) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "Offline users successfully retrieved",
                        "Offline_users" => $this->admin->getQueryResults()
                    )
                );
            } else {
                $this->sendJsonResponse(
                    status: "success",
                    content: array(
                        "message" => "Offline users cannot be retrieved",
                    )
                );
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // after notification is fixed
    public function broadcastMessages(array $args)
    {
        // this args array should contain the following
        // $args["message"] => "message_to_be_sent"
    }

    // after notification is fixed
    public function unicastMessages(array $args)
    {
        // this args array should contain the following
        // $args["id"] => "user_id_of_the_receiver"
        // $args["message"] => "message_to_be_sent"
    }
}
