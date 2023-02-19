<?php

namespace App\Controller\Authenticate;

require __DIR__ . "/../../../vendor/autoload.php";

use Core\Config;
use App\Model\Admin;
use Core\Validator\Validator;
use Core\Token;
use Core\Cookie;
use Core\Response;

class AdminAuthController extends Token
{
    private Admin $admin; // used to store the remember me session in database
    private array $errors;
    private Validator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->errors = array();
    }

    public function onAdminLogin(array $credentials = array())
    {
        if ($this->isLogged()) {
            session_start();
            $_SESSION["primary_role"] = "admin";
            header("Location: http://localhost/public/admin/dashboard");
        } else {
            if (!empty($credentials)) {
                try {
                    $remember_me = array_key_exists("remember_me", $credentials) ?
                        (!empty($credentials["remember_me"]) ?
                            true : false
                        ) : false;

                    if (array_key_exists("remember_me", $credentials))
                        unset($credentials["remember_me"]);

                    $this->validator = new Validator();
                    $this->admin = new Admin(); // this should be administrator's model not the admin's model

                    if ($this->adminLogin($credentials)) {

                        session_start();

                        $id = $this->admin->getAdminData()->id;
                        $username = $this->admin->getAdminData()->username;
                        $primary_role = "admin";

                        $this->setBearerTokenInCookie(
                            headers: array(
                                "alg" => "HS256",
                                "typ" => "JWT"
                            ),
                            payload: array(
                                "id" => $id,
                                "name" => $username,
                                "primary_role" => $primary_role,
                            )
                        );

                        $ttl = $remember_me ? "remember_me_ttl" : "refresh_ttl";
                        $this->setBearerTokenInCookie(
                            headers: array(
                                "alg" => "HS256",
                                "typ" => "JWT"
                            ),
                            payload: array(
                                "id" => $id,
                                "name" => $username,
                                "primary_role" => $primary_role,
                            ),
                            token_type: "refresh",
                            ttl: $ttl
                        );
                        $_SESSION["primary_role"] = $primary_role;
                        header("Location: http://localhost/public/admin/dashboard");
                    } else {
                        $this->sendResponse(
                            view: "/admin/login.html",
                            status: "unauthorized",
                            content: $this->errors
                        );
                    }
                } catch (\Exception $exception) {
                    throw $exception;
                }
            } else {
                return $this->sendResponse(
                    view: "/admin/login.html",
                    status: "unauthorized",
                    content: array("message" => "")
                );
            }
        }
    }

    public function adminLogin(array $credentials = array()): bool
    {
        if (!empty($credentials)) {
            try {
                $this->validator->validate(values: $credentials, schema: 'admin_login');
                if ($this->validator->getPassed()) {
                    if ($this->admin->readAdmin('username', $credentials["username"])) {
                        if (
                            password_verify(
                                $credentials["password"],
                                $this->admin->getAdminData()->password
                            )
                        ) {
                            return true;
                        } else {
                            $this->errors["message"] = "Validation errors in your inputs";
                            $this->errors["errors"] = "Incorrect password";
                            return false;
                        }
                    } else {
                        $this->errors["message"] = "Validation errors in your inputs";
                        $this->errors["errors"] = "Admin name cannot be found";
                        return false;
                    }
                } else {
                    $this->errors["message"] = "Validation errors in your inputs";
                    $this->errors["errors"] = array_merge([], $this->validator->getErrors());
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } else {
            $this->errors["message"] = "Validation errors in your inputs";
            $this->errors["errors"] = "Input fields are empty";
            return false;
        }
    }

    // make sure you check the user role and the user id in this function
    // check the role here otherwise normal admins can also go to the admin page as well.
    public function isLogged(): bool
    {
        if (
            Cookie::cookieExists(Config::getApiGlobal("remember")['access']) &&
            $this->validateToken($this->getBearerToken())
        ) {
            $payload = $this->getTokenPayload(
                Cookie::getCookieData(
                    Config::getApiGlobal("remember")['refresh']
                )
            );
            return true;
        } else {
            if (
                Cookie::cookieExists(Config::getApiGlobal("remember")["refresh"]) &&
                $this->validateToken($this->getBearerToken("refresh"))
            ) {
                if (Cookie::cookieExists(Config::getApiGlobal("remember")['access']))
                    Cookie::deleteCookie(Config::getApiGlobal("remember")['access']);

                $payload = $this->getTokenPayload(
                    Cookie::getCookieData(
                        Config::getApiGlobal("remember")['refresh']
                    )
                );
                if ($payload) {
                    if ($payload->primary_role == "admin") {
                        $id = $payload->id;
                        $username = $payload->name;
                        $primary_role = $payload->primary_role;
                        $this->setBearerTokenInCookie(
                            headers: array(
                                "alg" => "HS256",
                                "typ" => "JWT"
                            ),
                            payload: array(
                                "id" => $id,
                                "name" => $username,
                                "primary_role" => $primary_role,
                            )
                        );
                        return true;
                    }
                }
                return false;
            } else {
                return false;
            }
        }
    }

    public function logout()
    {
        session_start();
        if ($this->isLogged()) {
            if (Cookie::cookieExists(Config::getApiGlobal("remember")['access'])) {
                Cookie::deleteCookie(Config::getApiGlobal("remember")['access']);
            }
            if (Cookie::cookieExists(Config::getApiGlobal("remember")['refresh'])) {
                Cookie::deleteCookie(Config::getApiGlobal("remember")['refresh']);
            }
            session_destroy();
            $this->sendJsonResponse("success", ["message" => "admin successfully logged out"]);
        } else {
            $this->sendJsonResponse("unauthorized", ["message" => "Bad request"]);
        }
    }

    /* 
     * Function description
     * 
     * take the data inside of the token payload and return that data as a php object
     */
    public function getCredentials()
    {
        if (Cookie::cookieExists(Config::getApiGlobal("remember")['access'])) {
            return $this->getTokenPayload(Cookie::getCookieData(Config::getApiGlobal("remember")['access']));
        } else if (Cookie::cookieExists(Config::getApiGlobal("remember")['refresh'])) {
            return $this->getTokenPayload(Cookie::getCookieData(Config::getApiGlobal("remember")['refresh']));
        } else {
            return null;
        }
    }

    protected function sendResponse(int|string $view, string $status, array $content = array())
    {
        Response::sendResponse(view: $view, status: $status, content: $content);
    }

    protected function sendJsonResponse(string $status, array $content = array())
    {
        Response::sendJsonResponse(status: $status, content: $content);
    }
}
