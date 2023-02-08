<?php

namespace App\Controller\Authenticate;

use Core\Config;
use Core\Cookie;
use Core\Token;

abstract class AuthenticateController extends Token
{
    public function __construct()
    {
        parent::__construct();
    }

    abstract static public function isLogged();
    /*
     * Function description
     *
     * take the data inside the token payload and return that data as a php object
     */
    public static function getCredentials()
    {
        if (Cookie::cookieExists(Config::getApiGlobal("remember")['access'])) {
            return self::getTokenPayload(Cookie::getCookieData(Config::getApiGlobal("remember")['access']));
        } else if (Cookie::cookieExists(Config::getApiGlobal("remember")['refresh'])) {
            return self::getTokenPayload(Cookie::getCookieData(Config::getApiGlobal("remember")['refresh']));
        } else {
            return null;
        }
    }

    // abstract public function login();
    abstract public function logout();
}