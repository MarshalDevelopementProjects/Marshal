<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

/**
 * Class description
 * 
 * class encapsulates global configurations of the API
 * 
 */
class Config
{
    /**
     * @access private
     * @static
     * @var array $config contains the configurations
     */
    private static array $config = array(
        'mysql' => array(
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'db' => 'login_auth_rest_api'
        ),
        'linux_db_config' => array('connection' => 'mysql:host=127.0.0.1;dbname=login_auth_rest_api;port=3306', 'password' => 'password_is_2021_ID', 'username' => 'marshal'),
        // logging page remember functionality storage to store the cookie
        'remember' => array(

            // access token cookie config
            'access' => 'access_token',
            'access_ttl' => 60 * 10, // 10 mins

            // refresh token cookie config
            'refresh' => 'refresh_token',
            'refresh_ttl' => 60 * 60 * 24,

            // remember me token and cookie ttl
            'remember_me_ttl' => 60 * 60 * 24 * 14, // for now remember me cookie is set to last for two weeks 
        ),
    );

    /**
     * Function description
     *
     * Retrieve global api configurations such as database configs and cookie configs
     * @access public
     * @param string|null $key takes a key to search the configurations
     * @return array|string|null
     * @static
     */
    public static function getApiGlobal(string $key = null): string|array|null
    {
        return self::$config[$key] ?? null;
    }
}
