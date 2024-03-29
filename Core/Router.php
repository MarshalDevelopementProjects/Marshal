<?php

/* 
 * 
 * /login                            =>
 * /register                         =>
 * /project/projects                 =>
 * /project/projects?id=10           =>
 * /project/filter?filter_by=name    =>
 * /project/tasks                    =>
 * /project/tasks?id=12              => 
 * /admin/users                      => 
 * /admin/users?id=9                 => 
 * 
 */

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

use Exception;


/**
 * Class description
 *
 * Encapsulates routing behavior within the API
 *
 */
class Router
{

    /**
     * @access private
     * @var string METHOD_GET
     */
    private const METHOD_GET = 'GET';

    /**
     * @access private
     * @var string METHOD_POST
     */
    private const METHOD_POST = 'POST';

    /**
     * @access private
     * @var string METHOD_PUT
     */
    private const METHOD_PUT = 'PUT';

    /**
     * @access private
     * @var string METHOD_DELETE
     */
    private const METHOD_DELETE = 'DELETE';

    /**
     * @access private
     * @var array $route contains routing information
     */
    private array $routes;

    /**
     * @access private
     * @var array $params contains query parameters or the data sent in the body of the request
     */
    private array $params;


    public function __construct()
    {
        $this->routes = array();
        $this->params = array();
    }

    /**
     * Function description
     *
     * Registers a given route along with a pattern in the
     * @param string $method takes the http method as a string (GET, POST likewise)
     * @param string $pattern takes the route pattern of a particular route
     * @param callable|object|string $handler take a handler that will be triggered in case of dispatch
     * @return void @access private
     */
    private function add(string $method, string $pattern, callable|object|string $handler): void
    {
        $pattern = '{^' . $method . '/public' . $pattern . '$}'; // create a regular expression pattern(case sensitive)
        $this->routes[$pattern] = $handler;
    }

    /**
     * Function description
     *
     * Registers a given route pattern in the
     * @param string $path takes the route
     * @param callable|object|string $handler take a handler that will be triggered in case of dispatch
     * @return void @access public
     */
    public function get(string $path, callable|object|string $handler): void
    {
        $this->add(self::METHOD_GET, $path, $handler);
    }

    /**
     * Function description
     *
     * Registers a given route pattern in the
     * @param string $path takes the route
     * @param callable|object|string $handler take a handler that will be triggered in case of dispatch
     * @return void @access public
     */
    public function post(string $path, callable|object|string $handler): void
    {
        $this->add(self::METHOD_POST, $path, $handler);
    }

    /**
     * Function description
     *
     * Registers a given route pattern in the
     * @param string $path takes the route
     * @param callable|object|string $handler take a handler that will be triggered in case of dispatch
     * @return void @access public
     */
    public function put(string $path, callable|object|string $handler): void
    {
        $this->add(self::METHOD_PUT, $path, $handler);
    }

    /**
     * Function description
     *
     * Registers a given route along with a pattern in the
     * @param string $path takes the route
     * @param callable|object|string $handler take a handler that will be triggered in case of dispatch
     * @return void @access public
     */
    public function delete(string $path, callable|object|string $handler): void
    {
        $this->add(self::METHOD_DELETE, $path, $handler);
    }

    /**
     * Function description
     *
     * In case if the route requested cannot be matched with the registered routes in the @var array $routes
     * this function will be called
     *
     * @access private
     * @return void
     */
    private function requestNotFound(): void
    {
        Response::sendResponse(404, "error", array("message" => "Service cannot be found"));
    }

    /**
     * Function description
     *
     * If a route matches then, this function will get the parameter of that route and
     * will return a boolean value indicating the match
     *
     * @access private
     * @param string $pattern takes the route pattern of a particular route
     * @param string $target takes a target route
     * @return bool
     */
    private function match(string $pattern, string $target): bool
    {
        $params = Request::getQueryParams($pattern, $target);
        if ($params || preg_match($pattern, $target)) {
            if (is_array($params)) $this->params = $params;
            return true;
        }
        return false;
    }

    /**
     * Function description
     *
     * If a matching case is found for a particular request URI this function will
     * call the handler registered for the requested pattern with the data sent by
     * the client
     *
     * @access public
     * @param string $uri requested URI
     * @param array|null $data takes the arguments for the dispatch handler
     * @return void
     * @throws Exception if callback registered in the @var array $routes is not valid
     */
    public function dispatch(string $uri, null|array $data = array()): void
    {
        // GET . "/user/project" => GET/user/project
        $request = $_SERVER["REQUEST_METHOD"] . $uri;
        $matched = false;

        foreach ($this->routes as $pattern => $callback) {
            if ($matched = $this->match($pattern, $request)) {

                $data = $data !== null ? array_merge($this->params, $data) : $this->params;

                if (is_string($callback)) {
                    $parts = explode('::', $callback);
                    if ($parts) {
                        $class_name = $parts[0];
                        if (class_exists($class_name)) {
                            $controller_object = new $class_name();
                            $action = $parts[1];
                            if (is_callable([$controller_object, $action])) {
                                $controller_object->$action($data);
                            } else $this->requestNotFound();// throw new Exception("$action method cannot be found in the $class_name controller");
                        } else $this->requestNotFound();// throw new Exception("$class_name controller cannot be found");
                    } else $this->requestNotFound();// throw new Exception("Invalid callback format");
                } else {
                    if (is_callable($callback)) {
                        call_user_func_array($callback, array("data" => $data));
                    } else if (is_object($callback)) {
                        $callback->callback($data);
                    } else {
                        $this->requestNotFound();// throw new Exception("Invalid callback format");
                    }
                }
                break;
            }
        }
        if (!$matched) {
            $this->requestNotFound();
        }
    }
}
