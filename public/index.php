<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\BookController;
use App\Controllers\AuthController;

// Start session for authentication
session_start();

$router = new Router();

// Home routes
$router->get('/', 'App\Controllers\HomeController@index');

// Book routes
$router->get('/books', 'App\Controllers\BookController@index');
$router->get('/books/show', 'App\Controllers\BookController@show');
$router->get('/books/create', 'App\Controllers\BookController@create');
$router->post('/books/store', 'App\Controllers\BookController@store');
$router->get('/books/edit', 'App\Controllers\BookController@edit');
$router->post('/books/update', 'App\Controllers\BookController@update');
$router->post('/books/delete', 'App\Controllers\BookController@destroy');

// AUTH ROUTES - Make sure ALL of these are present!
$router->get('/login', 'App\Controllers\AuthController@showLogin');
$router->post('/login', 'App\Controllers\AuthController@login');
$router->get('/logout', 'App\Controllers\AuthController@logout');
$router->get('/register', 'App\Controllers\AuthController@showRegister');  // ← IMPORTANT!
$router->post('/register', 'App\Controllers\AuthController@register');      // ← IMPORTANT!

$router->resolve();