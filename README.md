# Library Web

A browser-based Library Management System built as a mini MVC framework **without** using Laravel, Symfony, or any other framework — built from scratch to understand what a framework actually does under the hood.

> **Status:** Assignments 1–3 complete (Composer Autoloading, Router, PDO Database Layer). Assignments 4–10 are not yet implemented.

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
- MySQL
- PDO
- Apache / PHP built-in server
- Bootstrap 5 (not yet added — Assignment 7)

## Architecture
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
Repositories (data access — MySQL via PDO)
│
v
Models (plain data objects, e.g. Book, User)


## Folder Structure

library-web/
├── app/
│ ├── Controllers/ # HomeController, BookController, AuthController
│ ├── Models/ # Book, User
│ ├── Services/ # LibraryService
│ ├── Repositories/ # BookRepositoryInterface, MySqlBookRepository, BookMapper
│ ├── Views/ # layouts/, books/, auth/, home/
│ └── Core/ # Router, Database, Request, Response, View
├── public/
│ └── index.php # Application entry point
├── composer.json
├── .gitignore
└── README.md


| Folder | Responsibility |
|---|---|
| `Controllers/` | Receive requests, call Services, return a response |
| `Models/` | Plain data objects — no logic beyond getters/setters |
| `Services/` | Business logic and validation rules |
| `Repositories/` | Data access — reading/writing books via PDO, regardless of storage backend |
| `Views/` | HTML templates (not yet used by controllers — Assignment 4) |
| `Core/` | Framework-level building blocks: routing, database connection, request/response wrappers |

## Design Decisions

- **PSR-4 autoloading over `require_once`** — every class is loaded automatically based on its namespace, matching how real-world PHP projects (and Composer packages) work.
- **Front controller pattern** — a single `public/index.php` handles every request, rather than one PHP file per page.
- **Interface-based repository (`BookRepositoryInterface`)** — chosen deliberately so the underlying storage can change without touching any controller or service code. Proven out in Assignment 3: the JSON-based repository was swapped for a PDO/MySQL-based one without changing `BookController` or `LibraryService` at all.
- **`::class` + string method dispatch** — mirrors the "Controller@method" convention used by Laravel, without needing its full routing engine.
- **Graceful failure over crashes** — the Router checks `class_exists()`/`method_exists()` before dispatching, returning `404`/`500` instead of raw fatal errors. Extended in Assignment 3 to also catch database connection failures.
- **Prepared statements throughout** — every SQL query in `MySqlBookRepository` uses bound parameters, avoiding SQL injection.
- **Normalized schema with `findOrCreate` helpers** — authors and categories live in their own tables, referenced from `books` via foreign keys. `findOrCreateAuthor()`/`findOrCreateCategory()` look up or insert as needed, avoiding duplicates.

## Assignment 1 — Composer Autoloading

**Goal:** Replace manual `require_once` statements with Composer's PSR-4 autoloading.

### What was done

1. Ran `composer init` to generate `composer.json`
2. Configured PSR-4 autoloading, mapping `App\` to `app/`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/"
    }
}
```

3. Created placeholder classes to confirm the autoloader resolves every namespace correctly.
4. Ran `composer dump-autoload`.

### Validation

No file in the project uses `require_once` — every class loads purely through its namespace via Composer's autoloader.

## Assignment 2 — Simple Router

**Goal:** Replace a `switch($_GET['page'])`-style dispatcher with a real Router class that maps HTTP method + URI to a controller action.

### What was done

1. Built `App\Core\Router` with `get()`/`post()` to register routes, and `resolve()` to match the current request and dispatch it.
2. Routes registered in `public/index.php`:

```php
$router->get('/books', BookController::class . '@index');
$router->post('/books', BookController::class . '@store');
```

3. Graceful error handling — `404` for unmatched routes, `500` for malformed/missing controller-method pairs.
4. Built the supporting data layer: `Book` model, `BookRepositoryInterface`, `JsonBookRepository` (temporary), `BookMapper`, `LibraryService`.

### Validation

`/books` dispatches through the Router into `BookController::index()`. Unregistered paths return `404`.

## Assignment 3 — PDO Database Layer

**Goal:** Replace JSON storage completely with MySQL via PDO. If the database is unreachable, display a clean error message instead of crashing.

### What was done

1. Built `App\Core\Database` — wraps a single, shared (`static`) PDO connection:
   - Constructor takes host, database name, username, password
   - `getConnection()` lazily connects on first use, with `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES => false`
   - `disconnect()` clears the shared connection
   - Connection failures are caught and rethrown with a clean message
2. Designed a normalized schema: `books`, `authors`, `category` tables, `books` referencing the other two via foreign keys
3. Built `App\Repositories\MySqlBookRepository`, implementing `BookRepositoryInterface` — swapped in place of `JsonBookRepository` with no changes required to `BookController` or `LibraryService`:
   - All queries use prepared statements with bound parameters
   - `findOrCreateAuthor()` / `findOrCreateCategory()` look up or insert related rows automatically
   - `hydrate()` maps a joined SQL row back into a `Book` object
4. `BookController`'s constructor wraps database/repository setup in a `try/catch`, storing a user-facing error message instead of letting an exception propagate

### Validation

- With the database running, `/books` correctly lists books sourced from MySQL (joined across `books`, `authors`, `category`)
- With the database unreachable (tested with an invalid database name), the app displays **"Database connection failed."** instead of crashing

### Known limitations (intentional, deferred to later assignments)

- Controllers still echo HTML directly — moves into `Core\View` + `Views/books/*.php` in Assignment 4
- Database credentials are currently passed directly in `BookController`'s constructor rather than loaded from environment variables — to be addressed as a follow-up cleanup

## Installation

**Requirements:** PHP 8.3+, Composer, MySQL

```bash
git clone <your-repo-url>
cd library-web
composer install
```

Import the schema (`books`, `authors`, `category` tables) into MySQL, then update the connection details in `BookController`'s constructor to match your local setup.

```bash
php -S localhost:8000 -t public
```

Then visit `http://localhost:8000`.

## Upcoming Work

- [ ] Assignment 4 — MVC separation (move HTML out of logic)
- [ ] Assignment 5 — Authentication (login/logout, password hashing)
- [ ] Assignment 6 — Session-protected routes
- [ ] Assignment 7 — CRUD interface with Bootstrap
- [ ] Assignment 8 — Search
- [ ] Assignment 9 — Pagination
- [ ] Assignment 10 — Custom 404/500 error pages

## Author

Ahmad Subhan