<?php

namespace App\Controller\Authenticate;

require __DIR__ . "/../../../vendor/autoload.php";

use Core\Config;
use App\Model\User;
use Core\Validator\Validator;
use Core\Token;
use Core\Cookie;
use Core\Response;

class UserAuthController extends Token
{
    private User $user; // used to store the remember me session in database
    private array $errors;
    private Validator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->errors = array();
    }

    public function onUserLogin(array $credentials = array())
    {
        if ($this->isLogged()) {
            session_start();
            $_SESSION["primary_role"] = "user";
            header("Location: http://localhost/public/user/dashboard");
            exit;
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
                    $this->user = new User();

                    if ($this->userLogin($credentials)) {

                        session_start();

                        $id = $this->user->getUserData()->id;
                        $name = $this->user->getUserData()->username;
                        $primary_role = "user";

                        $this->setBearerTokenInCookie(
                            headers: array(
                                "alg" => "HS256",
                                "typ" => "JWT"
                            ),
                            payload: array(
                                "id" => $id,
                                "name" => $name,
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
                                "name" => $name,
                                "primary_role" => $primary_role,
                            ),
                            token_type: "refresh",
                            ttl: $ttl
                        );
                        $_SESSION["primary_role"] = "user";
                        // set the state to "ONLINE"
                        $this->user->updateState(id: $this->user->getUserData()->id, state: "ONLINE");
                        header("Location: http://localhost/public/user/dashboard");
                        exit;
                    } else {
                        $this->sendResponse(
                            view: "/user/login.html",
                            status: "unauthorized",
                            content: $this->errors
                        );
                    }
                } catch (\Exception $exception) {
                    throw $exception;
                }
            } else {
                $this->sendResponse(
                    view: "/user/login.html",
                    status: "unauthorized",
                    content: array("message" => "Authentication credentials are empty")
                );
            }
        }
    }

    private function userLogin(array $credentials = array()): bool
    {
        if (!empty($credentials)) {
            try {
                $this->validator->validate(values: $credentials, schema: 'login');
                if ($this->validator->getPassed()) {
                    if ($this->user->readUser('username', $credentials["username"])) {
                        if (
                            password_verify(
                                $credentials["password"],
                                $this->user->getUserData()->password
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
                        $this->errors["errors"] = "Username cannot be found";
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

    public function onUserSignup(array $user_info = array())
    {
        if ($this->isLogged()) {
            header("Location: http://localhost/public/user/dashboard");
            exit;
        } else {
            $this->userSignup($user_info);
        }
    }
    public function userSignup(array $params = array())
    {
        if (!empty($params)) {
            try {
                $this->validator = new Validator();
                $this->user = new User();
                $this->validator->validate(values: $params, schema: "signup");
                if ($this->validator->getPassed()) {
                    unset($params["password_re_enter"]);

                    $this->user->createUser($params);
                    $this->sendResponse(
                        view: "/user/login.html",
                        status: "success",
                        content: array("message" => "User successfully registered")
                    );
                    exit;
                } else {
                    $this->errors["message"] = "Validation errors in your inputs";
                    $this->errors["errors"] = array_merge([], $this->validator->getErrors());
                    return $this->sendResponse("/user/signup.html", "error", $this->errors);
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } else {
            $this->errors["message"] = "Validation errors in your inputs";
            $this->errors["errors"] = "Input fields are empty";
            return $this->sendResponse("/user/signup.html", "error", $this->errors);
        }
    }

    // make sure you check the user role and the user id in this function
    public function isLogged(): bool
    {
        if ($this->validateToken($this->getBearerToken())) {
            return true;
        } else {
            if ($this->validateToken($this->getBearerToken("refresh"))) {
                if (Cookie::cookieExists(Config::getApiGlobal("remember")['access']))
                    Cookie::deleteCookie(Config::getApiGlobal("remember")['access']);

                $payload = $this->getTokenPayload(
                    Cookie::getCookieData(
                        Config::getApiGlobal("remember")['refresh']
                    )
                );
                if ($payload) {
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
                }
                return true;
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
            // set the state to "OFFLINE"
            try {
                $this->user->updateState(id: $this->user->getUserData()->id, state: "OFFLINE");
            } catch (\Exception $exception) {
                throw $exception;
            }
            session_destroy();
            $this->sendJsonResponse("success", ["message" => "user successfully logged out"]);
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
