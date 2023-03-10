<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

/**
 * Class description
 * 
 * encapsulates the behavior of a http states
 * 
 * @abstract 
 * @method static mixed getHeaderContent(string $status, string $http_method)
 */

abstract class Status
{
    /**
     * @access public
     * @var string
     */
    public const CONTENT_TYPE_JSON = "Content-Type: application/json";

    /**
     * @access private
     * @var array 
     */
    private const STATUS = array(
        "SUCCESS" => array(
            "GET" => 200,
            "POST" => 201,
            "PUT" => 200, // 200 means that the updated entity will be sent the client with the response
            // if the response need not be sent then set this to "204 No Content" for PUT requests
            "DELETE" => 204
        ),
        "ERROR" => array(
            "GET" => 404, // the request service is not there so the service is not found
            "POST" => 400, // request cannot be served ex:- user input errors
            "PUT" => 400, // if the resource to be updated cannot be found to be updated then 404
            "DELETE" => 404 // requested resource cannot be deleted(cannot be found to be deleted)
        ),
        // unauthorized means authentication credentials were missing or incorrect
        // ex:- "message": "Authentication credentials were missing or incorrect"
        "UNAUTHORIZED" => array(
            "GET" => 401,
            "POST" => 401,
            "PUT" => 401,
            "DELETE" => 401,
        ),
        // forbidden means access is not allowed or refused
        // ex:- "message": "The request is understood, but it has been refused or access is not allowed"
        "FORBIDDEN" => array(
            "GET" => 403,
            "POST" => 403,
            "PUT" => 403,
            "DELETE" => 403,
        ),
        "CONFLICT" => array(
            "GET" => 409,
            "POST" => 409,
            "PUT" => 409,
            "DELETE" => 409,
        ),
        "TOO_MANY_REQUESTS" => array(
            "GET" => 429,
            "POST" => 429,
            "PUT" => 429,
            "DELETE" => 429,
        ),
        "INTERNAL_SERVER_ERROR" => array(
            "GET" => 500,
            "POST" => 500,
            "PUT" => 500,
            "DELETE" => 500,
        ),
        "SERVICE_UNAVAILABLE" => array(
            "GET" => 503,
            "POST" => 503,
            "PUT" => 503,
            "DELETE" => 503,
        ),
        "NOT_IMPLEMENTED" => array(
            "GET" => 501,
            "POST" => 501,
            "PUT" => 501,
            "DELETE" => 501,
        )
    );

    /**
     * Function description
     * 
     * constructs the status header string with a given status with respect to the http method 
     * 
     * @param string $status must be a key in the above constant array ex:- SUCCESS or ERROR and likewise 
     * @return int
     */
    public static function getStatusCode(string $status): int
    {
        $status = strtoupper($status);
        if (array_key_exists($status, self::STATUS)) {
            return self::STATUS[$status][$_SERVER["REQUEST_METHOD"]];
        } else {
            return 500;
        }
    }
}
