<?php
// $router->get('/', HomeController::class . '@index');
// $router->get('/books', BookController::class . '@index');
// $router->post('/books', BookController::class . '@store');

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\BookController;

$router = new Router();

// Home route
$router->get('/', HomeController::class . '@index');

// Book routes
$router->get('/books', BookController::class . '@index');           // List all books
$router->get('/books/create', BookController::class . '@create');   // Show create form
$router->post('/books/store', BookController::class . '@store');
$router->get('/books/show', BookController::class . '@show');       // Show single book
$router->get('/books/edit', BookController::class . '@edit');       // Show edit form
$router->post('/books/update', BookController::class . '@update');  // Update book
$router->post('/books/delete', BookController::class . '@destroy'); // Delete book

$router->resolve();