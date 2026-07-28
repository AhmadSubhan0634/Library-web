<?php

namespace App\Core;

use PDOException;

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
                View::render('errors/500', ['message' => 'Controller or method could not be found.']);
                return;
            }

            try {
                $class=new $parts[0]();
                $class->{$parts[1]}();
            } catch (PDOException $e) {
                http_response_code(500);
                View::render('errors/500', ['message' => 'Database connection failed.']);
            } catch (\Throwable $e) {
                http_response_code(500);
                View::render('errors/500', ['message' => 'An unexpected error occurred.']);
            }
        }
        else{
            http_response_code(404);
            View::render('errors/404');
        }
    }
}
