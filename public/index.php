<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\BookController;
use App\Controllers\AuthController;

$router = new Router();

// Home route
$router->get('/', HomeController::class . '@index');

// Book routes
$router->get('/books', BookController::class . '@index');
$router->get('/books/create', BookController::class . '@create');
$router->post('/books/store', BookController::class . '@store');
$router->get('/books/show', BookController::class . '@show');
$router->get('/books/edit', BookController::class . '@edit');
$router->post('/books/update', BookController::class . '@update');
$router->post('/books/delete', BookController::class . '@destroy');

// Auth routes
$router->get('/login', AuthController::class . '@showLogin');
$router->post('/login', AuthController::class . '@login');
$router->get('/register', AuthController::class . '@showRegister');
$router->post('/register', AuthController::class . '@register');
$router->get('/logout', AuthController::class . '@logout');


$router->get('/logout', AuthController::class . '@logout');
$router->resolve();
