<?php

namespace App\Core;

class View{
    public static function render(string $view,array $params=[]): void{

        $path=__DIR__ . '/../Views/'.$view.'.php';

        if(!file_exists($path)){
            throw new \RuntimeException("Error.View not found: {$view}");
        }

        extract($params);

        include $path;
    }
}
