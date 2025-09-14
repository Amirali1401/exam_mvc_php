<?php 

namespace System\Router;

use ReflectionMethod;

class Routing {

    private $current_route;

    public function __construct() {
        global $current_route;
        $this->current_route = explode('/', trim($current_route, '/'));
    }

    public function run() {
        // مسیر کنترلر
        $controllerFile = realpath(__DIR__ . "/../../application/controllers/" . $this->current_route[0] . ".php");
        
        if (!$controllerFile || !file_exists($controllerFile)) {
            echo "404 - Controller file not found!";
            exit;
        }

        // include فایل کنترلر
        require_once $controllerFile;

        // اگر متد مشخص نشده بود => index
        $method = (count($this->current_route) >= 2) ? $this->current_route[1] : "index";

        // نام کلاس با فضای نام
        $class = "Application\\Controllers\\" . $this->current_route[0];

        if (!class_exists($class)) {
            echo "404 - Controller class not found!";
            exit;
        }

        $object = new $class();

        if (method_exists($object, $method)) {
            $reflection = new ReflectionMethod($class, $method);
            $parameterCount = $reflection->getNumberOfParameters();

            $params = array_slice($this->current_route, 2);

            if ($parameterCount <= count($params)) {
                call_user_func_array([$object, $method], $params);
            } else {
                echo "404 - Parameter error!";
            }
        } else {
            echo "404 - Method not found!";
        }
    }
}
