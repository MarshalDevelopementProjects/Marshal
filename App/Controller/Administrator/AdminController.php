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

    public function __construct(string|int $admin_id = null)
    {
        try {
            parent::__construct();
            $this->adminAuth = new AdminAuthController();
            if ($this->auth()) {
                $credentials = $this->adminAuth->getCredentials();
                if ($credentials->id) $this->admin = new Admin($credentials->id);
                else $this->admin = new Admin($credentials->id);
            } else if ($admin_id) {
                $this->admin = new Admin($admin_id);
            } else {
                $this->admin = new Admin();
            }
            $this->validator = new Validator();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function auth()
    {
        return $this->adminAuth->isLogged();
    }

    public function defaultAction()
    {
        if ($this->auth()) {
            $this->sendResponse(
                view: "/admin/dashboard.html",
                status: "success"
            );
        } else {
            $this->sendResponse(
                view: "/admin/login.html",
                status: "unauthorized"
            );
        }
    }
}
