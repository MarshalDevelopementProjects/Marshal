<?php

namespace App\Controller\Authenticate;

require __DIR__ . "/../../../vendor/autoload.php";

use Core\Config;
use App\Model\User;
use Core\Validator\Validator;
use Core\Token;
use Core\Cookie;
use Core\Response;
use Core\Mailer;

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
                        $this->user->updateState(id: $this->user->getUserData()->id, user_state: "ONLINE");
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
                    content: array("message" => "")
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
                        if ($this->user->getUserData()->verified == "TRUE") {
                            if ($this->user->getUserData()->access == "ENABLED") {
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
                                $this->errors["message"] = "You don't have access to our, since your has been disabled";
                                return false;
                            }
                        } else {
                            $this->errors["message"] = "You are not a verified user, please verify your email first";
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


                    $recipient_name = $params["first_name"] . " " . $params["last_name"];
                    $recipient_email_address = $params["email_address"];
                    $verification_code = bin2hex(random_bytes(16));
                    // $expires_at = time() + 60 * 2; // expires in two minutes
                    // verify the email
                    $postman = new Mailer();
                    $mail_sent = $postman->sendEmail(
                        recipients: [$recipient_email_address],
                        subject: "Verify Your Email Address for Marshal",
                        // please make this link look like a button
                        body: "Dear $recipient_name,\n\nWelcome to Marshal, the project management platform that makes it easy to collaborate and stay on top of your projects.We're excited to have you on board, but we need to verify that the email address you provided is correct.\n\nTo verify your email address and start using Marshal, please click the following verification link:\n\nVerification Link: http://localhost/public/user/signup/email/verification?email_address=$recipient_email_address&verification_code=$verification_code\n\n\nIf you did not sign up for Marshal, please ignore this email.\n\nThank you for choosing Marshal and we look forward to helping you manage your projects more effectively.\n\nBest regards,\nThe Marshal Team"
                    );
                    if ($mail_sent) {
                        // add the user to the user table
                        $params["verification_code"] = $verification_code;
                        $this->user->createUser($params);
                        $this->sendResponse(
                            view: "/user/verify.html",
                            status: "success",
                            content: array("message" => "User email verification pending")
                        );
                    } else {
                        $this->sendResponse(
                            view: "/errors/500.html",
                            status: "internal_server_error",
                            content: array("message" => "Verification email cannot be sent")
                        );
                    }
                } else {
                    $this->errors["message"] = "Validation errors in your inputs";
                    $this->errors["errors"] = array_merge([], $this->validator->getErrors());
                    return $this->sendResponse("/user/signup.html", "error", $this->errors);
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } else {
            $this->errors["message"] = "";
            return $this->sendResponse("/user/signup.html", "error", $this->errors);
        }
    }

    // make sure you check the user role and the user id in this function
    public function isLogged(): bool
    {
        if (
            Cookie::cookieExists(Config::getApiGlobal("remember")['access']) &&
            $this->validateToken($this->getBearerToken())
        ) {
            return true;
        } else {
            if (
                Cookie::cookieExists(Config::getApiGlobal("remember")['refresh']) &&
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
            // set the state to "OFFLINE"
            try {
                $this->user = new User($this->getCredentials()->id);
                $this->user->updateState(id: $this->user->getUserData()->id, user_state: "OFFLINE");
            } catch (\Exception $exception) {
                throw $exception;
            }
            if (Cookie::cookieExists(Config::getApiGlobal("remember")['access'])) {
                Cookie::deleteCookie(Config::getApiGlobal("remember")['access']);
            }
            if (Cookie::cookieExists(Config::getApiGlobal("remember")['refresh'])) {
                Cookie::deleteCookie(Config::getApiGlobal("remember")['refresh']);
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

    // send an email then get the code check the code
    public function forgotPassword()
    {
    }

    // need to have the following 
    // [
    // "email_address" => "user email address",
    // "verification_code" => "verification code"
    // ]
    public function verifyUserEmailOnSignUp(array $args)
    {
        if (!empty($args)) {
            try {
                $this->user = new User();
                // select the user from the user table using the email
                if (array_key_exists("email_address", $args) && $this->user->readUser("email_address", $args["email_address"])) {
                    // user can be verified
                    if ($this->user->getUserData()->verified === "FALSE") {
                        if (array_key_exists("verification_code", $args) && $this->user->getUserData()->verification_code === $args["verification_code"]) {
                            $this->user->updateVerified($this->user->getUserData()->id, "TRUE");
                            $this->sendResponse(
                                view: "/user/login.html",
                                status: "success",
                                content: array("message" => "User emails was successfully verified, login to proceed")
                            );
                        } else {
                            $this->sendResponse(
                                view: "/user/login.html",
                                status: "success",
                                content: array("message" => "Your verification code is not valid")
                            );
                        }
                    } else {
                        $this->sendResponse(
                            view: "/errors/login.html",
                            status: "internal_server_error",
                            content: array("message" => "You have already been verified, please login to continue")
                        );
                    }
                } else {
                    $this->sendResponse(
                        view: "/user/login.html",
                        status: "error",
                        content: array("message" => "Email verification failed, no such email was found")
                    );
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } else {
            $this->sendResponse(
                view: "/errors/404.html",
                status: "error",
                content: array("message" => "Bad request")
            );
        }
    }

    // need to have the following 
    // [
    // "email_address" => "user email address",
    // "verification_code" => "verification code"
    // ]
    public function verifyUserEmailOnForgotPassword(array $args)
    {
    }
}
