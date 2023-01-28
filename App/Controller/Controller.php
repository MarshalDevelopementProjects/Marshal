<?php

namespace App\Controller;

require __DIR__ . "/../../vendor/autoload.php";

use App\Controller\Authenticate\AuthenticateController;
use Core\Response;

/*
 * This abstract class defines the basic methods that a controller class should
 * implement. Each class can either provide a custom implementation for the methods
 * or can use the default implementation.
 */

abstract class Controller
{
    public function __construct()
    {
        session_start();
    }

    /* 
     * Function description
     * 
     * each class may or may not provide an implementation for this method
     * used to trigger the default action of a controller if no action was
     * mentioned in there request uri
     */
    abstract public function defaultAction(Object|array|string|int $optional = null);

    /**
     * Function description
     * 
     * each controller can implement this action, in case the requested action
     * of the controller cannot be found
     */
    public function actionNotFound()
    {
        $this->sendResponse("/errors/503.html", "service_unavailable", array("message" => "such a service cannot be found"));
    }

    /** 
     * Function description
     * 
     * All the controllers that implements this base controller can use this method 
     * to send data to the client this method cannot eb overridden by the implementor
     * since they must all adhere to this format of the response
     * 
     * $status is the state of the response and is a string ex:- error, success
     * (refer the Core\Status class for more information about valid status)
     * $content must be an associative array that can be json_encode.
     * currently the response can only be json
     * 
     * USE THIS METHOD IN THE CONTROLLERS TO SEND RESPONSES AND ONLY THIS METHOD
     * 
     */

    protected final function sendResponse(string $view, string $status, array $content = array(), array $headers = array())
    {
        /* echo "<pre>";
        var_dump(array("view" => $view, "status" => $status, "content" => $content, "headers" => $headers));
        echo "</pre>"; */
        Response::sendResponse(view: $view, status: $status, content: $content);
    }

    protected final function sendJsonResponse(string $status, array $content = array(), array $headers = array())
    {
        /* echo "<pre>";
        var_dump(array("status" => $status, "content" => $content, "headers" => $headers));
        echo "</pre>"; */
        Response::sendJsonResponse(status: $status, content: $content);
    }
}
