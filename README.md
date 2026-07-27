# Library Web

A browser-based Library Management System built as a mini MVC framework **without** using Laravel, Symfony, or any other framework — built from scratch to understand what a framework actually does under the hood.

> **Status:** Assignments 1–4 complete (Composer Autoloading, Router, PDO Database Layer, MVC/View separation). Assignments 5–10 are not yet implemented.

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

```
Browser
  │
  v
public/index.php   (front controller — the single entry point for every request)
  │
  v
Core\Router          (matches HTTP method + URI to a Controller action)
  │
  v
Controllers            (receive the request, delegate to a Service, render a View)
  │
  v
Services                  (business logic — e.g. "don't add a book with a duplicate ISBN")
  │
  v
Repositories                 (data access — MySQL via PDO)
  │
  v
Models                          (plain data objects, e.g. Book, User)

Controllers also hand data off sideways to:
Core\View                (renders a template from app/Views/ and returns/echoes HTML)
```

## Folder Structure

```
library-web/
├── app/
│   ├── Controllers/     # HomeController, BookController, AuthController
│   ├── Models/          # Book, User
│   ├── Services/        # LibraryService
│   ├── Repositories/    # BookRepositoryInterface, MySqlBookRepository, BookMapper
│   ├── Views/
│   │   ├── layouts/     # Shared page structure (header/footer wrapper)
│   │   ├── books/       # index, show, create, edit, store, update, delete
│   │   ├── auth/        # (placeholder — Assignment 5)
│   │   ├── home/        # index
│   │   └── errors/      # error.php (shared 404/500 template)
│   └── Core/            # Router, Database, Request, Response, View
├── public/
│   └── index.php        # Application entry point
├── composer.json
├── .gitignore
└── README.md
```

| Folder | Responsibility |
|---|---|
| `Controllers/` | Receive requests, call Services, pass data to a View — contain no HTML |
| `Models/` | Plain data objects — no logic beyond getters/setters |
| `Services/` | Business logic and validation rules |
| `Repositories/` | Data access — reading/writing books via PDO |
| `Views/` | HTML templates only — no SQL, no business logic |
| `Core/` | Framework-level building blocks: routing, database connection, request/response wrappers, view rendering |

## Design Decisions

- **PSR-4 autoloading over `require_once`** — every class is loaded automatically based on its namespace, matching how real-world PHP projects (and Composer packages) work.
- **Front controller pattern** — a single `public/index.php` handles every request, rather than one PHP file per page.
- **Interface-based repository (`BookRepositoryInterface`)** — chosen deliberately so the underlying storage can change without touching any controller or service code. Proven out in Assignment 3: the JSON-based repository was swapped for a PDO/MySQL-based one without changing `BookController` or `LibraryService` at all.
- **`::class` + string method dispatch** — mirrors the "Controller@method" convention used by Laravel, without needing its full routing engine.
- **Graceful failure over crashes** — the Router checks `class_exists()`/`method_exists()` before dispatching, returning `404`/`500` instead of raw fatal errors. Extended in Assignment 3 to also catch database connection failures.
- **Prepared statements throughout** — every SQL query in `MySqlBookRepository` uses bound parameters, avoiding SQL injection.
- **Normalized schema with `findOrCreate` helpers** — authors and categories live in their own tables, referenced from `books` via foreign keys. `findOrCreateAuthor()`/`findOrCreateCategory()` look up or insert as needed, avoiding duplicates.
- **Controllers hand data to Views instead of echoing HTML** — `Core\View` centralizes template rendering, so every controller action just prepares data and calls one method rather than building markup inline.

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

## Assignment 4 — Build MVC

**Goal:** Move all HTML out of controller logic and into dedicated View files, so controllers contain no markup, views contain no SQL, and models contain no HTML.

### What was done

1. Built `App\Core\View` to centralize template rendering — given a view path (e.g. `books/index`) and a data array, it locates the matching file under `app/Views/`, extracts the data into local variables, and includes the template.
2. Removed every `echo "<h1>...</h1>"`-style line from `BookController` and `HomeController`. Each action now:
   - Gathers data from `LibraryService` (or nothing, for static pages)
   - Passes that data to the matching View
3. Created the view files under `app/Views/`:
   - `books/index.php` — book list
   - `books/show.php` — single book detail
   - `books/create.php` — add-book form
   - `books/edit.php` — edit-book form
   - `books/store.php`, `books/update.php`, `books/delete.php` — result/confirmation templates for POST actions
   - `home/index.php` — landing page
   - `errors/error.php` — shared template for 404/500 messages, reused by the Router
4. Updated `App\Core\Router` to render `errors/error.php` (via `View`) for 404/500 cases instead of a raw `echo` string, keeping error output consistent with the rest of the app.

### Validation

- Inspected every file in `Controllers/` — none contain HTML tags or inline markup
- Inspected every file in `Views/` — none contain SQL or direct repository/service calls; they only read variables passed in by the controller
- Inspected `Models/` — contain only properties, constructors, and getters/setters
- `/books`, `/books/show`, `/books/create`, `/books/edit` all render through their respective View files and display correctly in the browser

### Known limitations (intentional, deferred to later assignments)

- Views currently use plain HTML — Bootstrap styling comes in Assignment 7
- No shared header/footer layout wrapper is enforced yet across all views — `layouts/` exists but full layout composition is being finalized alongside Assignment 7
- Database credentials are currently passed directly in `BookController`'s constructor rather than loaded from environment variables — to be addressed as a follow-up cleanup
- `AuthController` still has no real logic — depends on the `users` table and password hashing from Assignment 5

## Installation

**Requirements:** PHP 8.3+, Composer, MySQL

```bash
git clone <your-repo-url>
cd library-web
composer install
```

Import the schema (`books`, `authors`, `category` tables) into MySQL, then update the connection details in `BookController`'s constructor to match your local setup.

```bash
php -S localhost:8000 -t public public/index.php
```

Then visit `http://localhost:8000`.

## Upcoming Work

- [ ] Assignment 5 — Authentication (login/logout, password hashing)
- [ ] Assignment 6 — Session-protected routes
- [ ] Assignment 7 — CRUD interface with Bootstrap
- [ ] Assignment 8 — Search
- [ ] Assignment 9 — Pagination
- [ ] Assignment 10 — Custom 404/500 error pages

## Author

Ahmad Subhan