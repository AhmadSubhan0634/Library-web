<?php

namespace App\Core;

class Router{

    // Routes array grouped by HTTP method first, then by URI.
    private array $routes=[];

    public function get(string $uri,string $action): void{
        $this->routes['GET'][$uri]=$action;
    }

    public function post(string $uri,string $action): void{
        $this->routes['POST'][$uri]=$action;
    }

    // Reads the current request and dispatches it to the matching controller/action.
    public function resolve(): void{

        $method=$_SERVER['REQUEST_METHOD'];

        // Strips off any query string
        $path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);

        if(isset($this->routes[$method][$path])){
            $action=$this->routes[$method][$path];

            // Split on '@' to get the class name and the method.
            $parts=explode('@',$action);

            // Checks to guard against a malformed action
            if(count($parts) !== 2 || !class_exists($parts[0]) || !method_exists($parts[0],$parts[1])){
                http_response_code(500);
                echo "Error. Controller or method is not found";
                return;
            }

            $class=new $parts[0]();
            $class->{$parts[1]}();

        }
        else{
            http_response_code(404);
            echo "404 - Route not found";
        }
    }
}