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
                    if ($credentials->id) $this->admin = new Admin($credentials->id);
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
            $this->sendResponse(
                view: 404,
                status: "error",
                content: array("message" => "No such user, the user id of the user is not valid")
            );
            exit;
            throw $exception;
        }
    }

    public function auth()
    {
        return $this->adminAuth->isLogged();
    }

    public function defaultAction(Object|array|string|int $optional = null)
    {
        $this->sendResponse(
            view: "/admin/dashboard.html",
            status: "success",
            content: array("message" => "Welcome")
        );
        exit;
    }
}
