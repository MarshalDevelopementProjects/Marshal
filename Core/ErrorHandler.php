<?php
// class used to maintain nad log error to log files and
// handle exception that are triggered by the controllers
// and the Router class when routing

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

use Core\Response;


/**
 * Class description
 * 
 * Encapsulates error handling details used to send error
 * in development to the front end to identify what went
 * wrong
 *
 */
class ErrorHandler
{
    /** 
     * Function description
     * 
     * Logs and sends json responses when an error occurs
     * Look at the console for error messages in the browser or the json response
     * @static
     * @return void
     * 
     * Function will log the error as well in the logs/ directory with a suffix "-error"
     * 
     */
    public static function errorHandler($error_number, $error_string, $file_name, $line_number): void
    {
        $file = date("Y-m-d") . "-error";
        $content = array(
            "Error number" =>  $error_number,
            "Error string" => $error_string,
            "File name" => $file_name,
            "Line number" => $line_number,
            "Time stamp" => date("M,d,Y h:i:s A")
        );

        file_put_contents(__DIR__ . "/../Logs/" . $file . ".txt", json_encode($content) . "\n", FILE_APPEND);
        Response::sendResponse(
            view: "505",
            status: "internal_server_error",
            content: $content
        );
    }

    /** 
     * Function description
     * 
     * Set http status codes and send a json response to the
     * client
     * 
     * When throwing new Exceptions use this as the message format when developing
     * throw new \Exception(
     *      json_encode(array(
     *          "status" =>  "HTTP 404 (or other error codes depending on the exception),
     *          "message": $exception->getMessage(), (can get this using the $exception->getMessage()),
     *          "file_name" : $exception->getFile(),
     *          "line_number" : $exception->getLine(),
     *          "stack_trace" : $exception->getTraceAsString(),
     *      ));
     * );
     * 
     * Decode the json encoded string sent with the exception and get status code
     * Set the header and send the json_encoded message to the client(REMEMBER THIS FUNCTION IS ONLY FOR
     * DEVELOPMENT PURPOSES)
     * 
     * When deploying or otherwise remove the attributes in the $content array as needed
     * and only send a status as error, not_implemented
     * 
     * @static
     * @param \Exception|\Error $exception takes an exception or an error
     * @return void
     * 
     * Function will log the error as well in the logs/ directory with a suffix "-exception"
     * 
     */
    public static function exceptionHandler(\Exception|\Error $exception): void
    {
        $content = array(
            "message" => $exception->getMessage(),
            "file_name" => $exception->getFile(),
            "line_number" => $exception->getLine(),
            "stack_trace" => $exception->getTraceAsString(),
        );

        $file = date("Y-m-d") . "-exceptions";
        file_put_contents(__DIR__ . "/../Logs/" . $file . ".txt", json_encode($content) . "\n", FILE_APPEND);
        Response::sendResponse(
            view: "505",
            status: "internal_server_error",
            content: $content
        );
    }
}
