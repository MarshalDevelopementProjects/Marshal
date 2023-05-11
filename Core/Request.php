<?php

namespace Core;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Class description
 * 
 * Encapsulates the actions to be performed on a query string
 * or query data of a request
 * 
 * NOTE : DATA SENT IN THE BODY OF THE HTTP METHOD MUST BE JSON ENCODED
 * 
 */

class Request
{
    /**
     * Function description
     * 
     * @static
     * @access public
     * @param string $req_data - request body
     * @return array
     * will return user data sent using various http methods decoded
     * as an associative array when requesting.
     * if there is no data then will return null.
     * 
     * Assumed the body of the request is json encoded hence
     * $req_data if supplied must be the JSON string
     * 
     */
    public static function getData(string $req_data = ""): null|array
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET)) {
            return self::sanitize($_GET);
        } else if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST)) {
            return self::sanitize($_POST);
        } else {
            if (!empty($req_data)) {
                $data = json_decode($req_data, true);
                if (!empty($data)) self::sanitize($data);
                return $data;
            }
            return array();
        }
    }

    /**
     * Function description
     * 
     * @static
     * @access private
     * @return array
     * 
     * Sanitizes the data sent by the client in the body or the URI of the request
     * 
     */
    private static function sanitize(array $args = array()): array
    {
        if (!empty($args)) {
            foreach ($args as $key => $value) {
                $key = htmlspecialchars(trim($key), ENT_QUOTES, 'UTF-8');
                $value = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
                $args[$key] = $value;
            }
        }
        return $args;
    }

    /**
     * Function description
     * 
     * @static
     * @access public
     * @param string $route - route of requested
     * @param string $uri - requested URI
     * @return array|bool
     * 
     * Extracts query params from the requested URI or returns false if there is nothing to parse
     * 
     * this function does two thing not very oop
     * 
     */
    public static function getQueryParams(string $route, string $uri): array|bool
    {
        if (preg_match($route, $uri, $matches)) {
            $params = array_filter($matches, fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);
            $params = self::sanitize($params);
            return !empty($params) ? $params : true;
        }
        return false;
    }
}
