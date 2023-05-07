<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

use Core\View;

class Response
{
    /*
     * Function description
     * 
     * @static
     * @access public
     * @return void
     * 
     * this function will handle responses
     * each response must be of the following form
     * 
     * A valid status taken from the Status abstract class in the Core(go to the class and see)
     * 
     * @param string $status is a string depending on the status
     * @param array $content must be an array with the appropriate content to be sent to the client, by default
     * @param string $content_type ONLY JSON IS PERMITTED FOR NOW
     * content is set to an array with message set to an empty string
     * 
     */
    /* public static function sendResponse(string $status = "success", array $content = array("message" => ""), $headers = array(), $content_type = "JSON"): void
    {
        header(Status::getHeaderContent(strtoupper($status), $_SERVER["REQUEST_METHOD"]));
        if ($content_type == "JSON") header(Status::CONTENT_TYPE_JSON);
        else {
            throw new \Exception("Any content type other that JSON is not supported at the moment");
            die();
        }
        if (!empty($headers)) {
            foreach ($headers as $key => $values) {
                if (is_array($values)) {
                    throw new \Exception("Not implemented");
                } else {
                    header("$key: $values");
                }
            }
        }
        echo json_encode($content);
    } */

    public static function sendResponse(string $view, string $status, array $content): void
    {
        http_response_code(Status::getStatusCode($status));
        View::render(view: $view, args: $content);
        exit;
    }

    public static function sendJsonResponse(string $status, array $content): void
    {
        http_response_code(Status::getStatusCode($status));
        header("Content-Type: application/json");
        echo json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
