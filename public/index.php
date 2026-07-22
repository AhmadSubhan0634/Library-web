<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\BookController;

$router = new Router();

$router->get('/', HomeController::class . '@index');
$router->get('/books', BookController::class . '@index');
$router->post('/books', BookController::class . '@store');

$router->resolve();