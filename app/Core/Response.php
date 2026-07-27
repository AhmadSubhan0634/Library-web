<?php

namespace App\Core;

class Response{
    
    public static function redirect(string $uri): void{
        header("Location: {$uri}");
        exit;
    }

    public static function setStatusCode(int $code): void{ http_response_code($code); }
}