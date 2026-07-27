<?php

// The ONLY require in the entire application.
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Controllers\HomeController;
use App\Controllers\BookController;
use App\Controllers\AuthController;
use App\Models\Book;
use App\Models\User;

$classes = [
    Router::class,
    Database::class,
    Request::class,
    Response::class,
    View::class,
    HomeController::class,
    BookController::class,
    AuthController::class,
    Book::class,
    User::class,
];

echo "Composer autoloading check:\n\n";

foreach ($classes as $class) {
    $loaded = class_exists($class) ? 'OK' : 'FAILED';
    echo "{$class} ... {$loaded}\n";
}
