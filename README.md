# Library Web

A browser-based Library Management System built as a mini MVC framework **without** using Laravel, Symfony, or any other framework — built from scratch to understand what a framework actually does under the hood.

> **Status:** Assignments 1–2 complete (Composer Autoloading, Router). Assignments 3–10 are not yet implemented.

## Project Overview

This project reimplements a console-based Library System as a real browser-based web application. The goal isn't to build "another CRUD app" — it's to understand what tools like Laravel actually do internally by building the equivalent pieces manually:

- How an HTTP request travels from browser to server and back
- How routing decides which code runs for a given URL
- How Composer's autoloader replaces dozens of manual `require` statements
- How MVC separates data (Models), display (Views), and decision-making (Controllers)
- How a database layer, authentication, and sessions fit into all of this

By the end, the app will let users browse, search, add, edit, and delete books through a proper web UI backed by MySQL — but every layer along the way is built by hand first.

## Tech Stack

- PHP 8.3+
- Composer
- MySQL (not yet connected — Assignment 3)
- PDO (not yet implemented — Assignment 3)
- Apache / PHP built-in server
- Bootstrap 5 (not yet added — Assignment 7)

## Architecture

The application follows a simplified MVC (Model-View-Controller) pattern with an additional Service/Repository layer for data access:

Browser
│
v
public/index.php (front controller — the single entry point for every request)
│
v
Core\Router (matches HTTP method + URI to a Controller action)
│
v
Controllers (receive the request, delegate to a Service, return output)
│
v
Services (business logic — e.g. "don't add a book with a duplicate ISBN")
│
v
Repositories (data access — currently JSON file, MySQL/PDO from Assignment 3)
│
v
Models (plain data objects, e.g. Book, User)

## Folder Structure

library-web/
├── app/
│ ├── Controllers/ # HomeController, BookController, AuthController
│ ├── Models/ # Book, User
│ ├── Services/ # LibraryService
│ ├── Repositories/ # BookRepositoryInterface, JsonBookRepository, BookMapper
│ ├── Views/ # layouts/, books/, auth/, home/
│ └── Core/ # Router, Database, Request, Response, View
├── public/
│ └── index.php # Application entry point
├── storage/
│ └── books.json # Temporary JSON data store (until Assignment 3 replaces it with MySQL/PDO)
├── composer.json
├── .gitignore
└── README.md

| Folder | Responsibility |
|---|---|
| `Controllers/` | Receive requests, call Services, return a response |
| `Models/` | Plain data objects — no logic beyond getters/setters |
| `Services/` | Business logic and validation rules |
| `Repositories/` | Data access — reading/writing books, regardless of storage backend |
| `Views/` | HTML templates (not yet used by controllers — Assignment 4) |
| `Core/` | Framework-level building blocks: routing, database connection, request/response wrappers |

## Design Decisions

- **PSR-4 autoloading over `require_once`** — every class is loaded automatically based on its namespace, matching how real-world PHP projects (and Composer packages) work.
- **Front controller pattern** — a single `public/index.php` handles every request, rather than one PHP file per page. This is what makes centralized routing, authentication checks, and error handling possible.
- **Interface-based repository (`BookRepositoryInterface`)** — chosen deliberately so the underlying storage (JSON now, MySQL later) can change without touching any controller or service code. This mirrors how real frameworks abstract database access.
- **`::class` + string method dispatch (`'App\Controllers\BookController@index'`)** — avoids hardcoding fully-qualified class names as raw strings, and mirrors the "Controller@method" convention used by Laravel and similar frameworks, without needing their full routing engine.
- **Graceful failure over crashes** — the Router checks `class_exists()`/`method_exists()` before dispatching, and returns proper HTTP status codes (`404`, `500`) instead of letting PHP throw raw fatal errors at users.
- **JSON storage as a temporary placeholder** — used only so the Router/Controller/Service chain could be built and tested end-to-end before the database layer exists. It's intentionally disposable.

## Assignment 1 — Composer Autoloading

**Goal:** Replace manual `require_once` statements with Composer's PSR-4 autoloading.

### What was done

1. Ran `composer init` to generate `composer.json`
2. Configured PSR-4 autoloading, mapping the `App\` namespace to the `app/` directory:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/"
    }
}
```

3. Created placeholder classes across `Controllers/`, `Models/`, and `Core/` to confirm the autoloader resolves every namespace correctly.
4. Ran `composer dump-autoload` to generate the autoloader.

### Validation

No file in the project uses `require_once` — every class loads purely through its namespace via Composer's autoloader.

At the time Assignment 1 was completed, a temporary check in `public/index.php` confirmed every placeholder class resolved correctly:

(This test script has since been replaced by the real application entry point built in Assignment 2.)

## Assignment 2 — Simple Router

**Goal:** Replace a `switch($_GET['page'])`-style dispatcher with a real Router class that maps HTTP method + URI to a controller action.

### What was done

1. Built `App\Core\Router` with:
   - `get(string $uri, string $action)` and `post(string $uri, string $action)` — register routes, grouped by HTTP method
   - `resolve()` — reads the current request (`$_SERVER['REQUEST_METHOD']` and `$_SERVER['REQUEST_URI']`), strips any query string via `parse_url()`, and matches it against registered routes
2. Routes are registered in `public/index.php` and dispatched to the correct controller/method, e.g.:

```php
$router->get('/books', BookController::class . '@index');
$router->post('/books', BookController::class . '@store');
```

3. Added graceful error handling in `resolve()`:
   - Returns a proper `404` status with a "Route not found" message if no route matches
   - Returns a `500` status if the action string is malformed, or the target controller/method doesn't exist
4. Built out a supporting data layer for `BookController` to actually have something to display:
   - `App\Models\Book` — the Book entity
   - `App\Repositories\BookRepositoryInterface` — contract for data access
   - `App\Repositories\JsonBookRepository` — temporary JSON-file-based implementation (to be replaced by a PDO/MySQL repository in Assignment 3)
   - `App\Repositories\BookMapper` — converts between `Book` objects and array/JSON data
   - `App\Services\LibraryService` — business logic layer (add/list/search/update/delete books), used by the controller instead of talking to the repository directly

### Validation

Visiting `/books` in the browser correctly dispatches through the Router into `BookController::index()`, which lists books via `LibraryService` (currently backed by a JSON file, showing "No books found." until data is added). Visiting an unregistered path returns a `404`.

### Known limitations (intentional, deferred to later assignments)

- `BookController::index()` still echoes HTML directly — this will move into `Core\View` + `Views/books/index.php` in Assignment 4
- `BookController::store()` does not exist yet — the POST `/books` route is registered ahead of time, but the method will be implemented once the "Add Book" form exists in Assignment 7
- Data is stored in `storage/books.json`, not MySQL — this is temporary scaffolding until Assignment 3 introduces PDO

## Installation

**Requirements:** PHP 8.3+, Composer

```bash
git clone <your-repo-url>
cd library-web
composer install
php -S localhost:8000 -t public
```

Then visit `http://localhost:8000` in your browser.

## Upcoming Work

- [ ] Assignment 3 — PDO Database Layer
- [ ] Assignment 4 — MVC separation (move HTML out of logic)
- [ ] Assignment 5 — Authentication (login/logout, password hashing)
- [ ] Assignment 6 — Session-protected routes
- [ ] Assignment 7 — CRUD interface with Bootstrap
- [ ] Assignment 8 — Search
- [ ] Assignment 9 — Pagination
- [ ] Assignment 10 — Custom 404/500 error pages

## Author

Ahmad Subhan