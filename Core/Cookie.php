<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

/**
 * Class description
 * 
 * Encapsulates the behavior of handling cookies
 * 
 * @method setCookie
 * @method cookieExists
 * @method getCookieData
 * @method deleteCookie
 * 
 */
class Cookie
{
    /**
     * Method description 
     * 
     * Set a cookie with the given value and settings 
     * 
     * @static
     * @access public
     * @param string $name takes the name used to identify the cookie
     * @param string $value takes the value to be stored in the cookie
     * @param int $expiry takes the time in seconds that the cookie will live for stating from now
     * @param bool $httpOnly is to set a http-only cookie, default set to true
     * @return bool if the cookie was successful set then true else false
     * 
     */
    public static function setCookie(string $name, string $value, $expiry, bool $httpOnly = true): bool
    {
        return setcookie(
            name: $name,
            value: $value,
            expires_or_options: [
                "expires" => time() + $expiry,
                "secure" => true,
                "httponly" => $httpOnly,
                "path" => "/",
                "domain" => "",
                "samesite" => "None"
            ]
        );
    }

    /**
     * Method description
     * 
     * Check if a cookie is set(exists) or not in the request
     * @static
     * @access public
     * @param string $name takes the name of the cookie 
     * @return bool if the cookie is set then true else false
     * 
     */
    public static function cookieExists(string $name): bool
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    /**
     * Method description
     * 
     * If a cookie is set then get the hash value or data in that cookie
     * @static
     * @access public
     * @param string $name takes the name of the cookie
     * @return string value stored in the cookie if the cookie exists
     * 
     */
    public static function getCookieData(string $name): string
    {
        return $_COOKIE[$name];
    }

    /**
     * Method description
     * 
     * If a cookie is set then this function is used to unset the cookie
     * @static
     * @access public
     * @param string $name takes the name of the cookie
     * @return void
     * 
     */
    public static function deleteCookie(string $name): void
    {
        self::setCookie(name: $name, value: '', expiry: time() - 1);
    }
}
