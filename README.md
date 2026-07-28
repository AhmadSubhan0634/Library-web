# Library Web

A browser-based Library Management System built as a mini MVC framework **without** using Laravel, Symfony, or any other framework — built from scratch to understand what a framework actually does under the hood.

> **Status:** Assignments 1–8 complete (Composer Autoloading, Router, PDO Database Layer, MVC/View separation, Authentication). Assignments 9–10 are not yet implemented.

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
Controllers            (receive the request, delegate to a Service/Repository, render a View)
  │
  v
Services / Repositories   (business logic + data access — MySQL via PDO)
  │
  v
Models                      (plain data objects, e.g. Book, User)

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
│   ├── Repositories/    # BookRepositoryInterface, MySqlBookRepository, BookMapper,
│   │                    # UserRepositoryInterface, MySqlUserRepository
│   ├── Views/
│   │   ├── layouts/     # Shared page structure (header/footer wrapper)
│   │   ├── books/       # index, show, create, edit, store, update, delete
│   │   ├── auth/        # login, register
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
| `Controllers/` | Receive requests, call Services/Repositories, pass data to a View — contain no HTML |
| `Models/` | Plain data objects — no logic beyond getters/setters |
| `Services/` | Business logic and validation rules |
| `Repositories/` | Data access — reading/writing books and users via PDO |
| `Views/` | HTML templates only — no SQL, no business logic |
| `Core/` | Framework-level building blocks: routing, database connection, request/response wrappers, view rendering |

## Design Decisions

- **PSR-4 autoloading over `require_once`** — every class is loaded automatically based on its namespace, matching how real-world PHP projects (and Composer packages) work.
- **Front controller pattern** — a single `public/index.php` handles every request, rather than one PHP file per page.
- **Interface-based repositories (`BookRepositoryInterface`, `UserRepositoryInterface`)** — chosen deliberately so the underlying storage can change without touching any controller or service code. Proven out in Assignment 3: the JSON-based book repository was swapped for a PDO/MySQL-based one without changing `BookController` or `LibraryService` at all. The same pattern was reused for users in Assignment 5.
- **`::class` + string method dispatch** — mirrors the "Controller@method" convention used by Laravel, without needing its full routing engine.
- **Graceful failure over crashes** — the Router checks `class_exists()`/`method_exists()` before dispatching, returning `404`/`500` instead of raw fatal errors. Extended in Assignment 3 to also catch database connection failures.
- **Prepared statements throughout** — every SQL query in `MySqlBookRepository` and `MySqlUserRepository` uses bound parameters, avoiding SQL injection.
- **Controllers hand data to Views instead of echoing HTML** — `Core\View` centralizes template rendering, so every controller action just prepares data and calls one method rather than building markup inline.
- **Passwords are never stored or compared in plaintext** — `password_hash()` on registration, `password_verify()` on login, per Assignment 5's requirement.

## Assignment 1 — Composer Autoloading

**Goal:** Replace manual `require_once` statements with Composer's PSR-4 autoloading.

### What was done

1. Ran `composer init` to generate `composer.json`
2. Configured PSR-4 autoloading, mapping `App\` to `app/`
3. Created placeholder classes to confirm the autoloader resolves every namespace correctly
4. Ran `composer dump-autoload`

### Validation

No file in the project uses `require_once` — every class loads purely through its namespace via Composer's autoloader.

## Assignment 2 — Simple Router

**Goal:** Replace a `switch($_GET['page'])`-style dispatcher with a real Router class that maps HTTP method + URI to a controller action.

### What was done

1. Built `App\Core\Router` with `get()`/`post()` to register routes, and `resolve()` to match the current request and dispatch it
2. Graceful error handling — `404` for unmatched routes, `500` for malformed/missing controller-method pairs
3. Built the supporting data layer: `Book` model, `BookRepositoryInterface`, `JsonBookRepository` (temporary), `BookMapper`, `LibraryService`

### Validation

`/books` dispatches through the Router into `BookController::index()`. Unregistered paths return `404`.

## Assignment 3 — PDO Database Layer

**Goal:** Replace JSON storage completely with MySQL via PDO. If the database is unreachable, display a clean error message instead of crashing.

### What was done

1. Built `App\Core\Database` — wraps a single, shared PDO connection, with a `disconnect()` method to release it
2. Designed a normalized schema: `books`, `authors`, `category` tables
3. Built `App\Repositories\MySqlBookRepository`, swapped in place of `JsonBookRepository` with no changes required to `BookController` or `LibraryService`
4. `BookController`'s constructor wraps database/repository setup in a `try/catch`, storing a user-facing error message instead of letting an exception propagate

### Validation

- With the database running, `/books` correctly lists books sourced from MySQL
- With the database unreachable, the app displays a database connection error instead of crashing

## Assignment 4 — Build MVC

**Goal:** Move all HTML out of controller logic and into dedicated View files.

### What was done

1. Built `App\Core\View` to centralize template rendering
2. Removed HTML from `BookController` and `HomeController` — each action gathers data and hands it to a View
3. Created view files under `app/Views/books/` and `app/Views/home/`, plus a shared `errors/error.php`

### Validation

- `Controllers/` (with one noted exception below) contain no HTML tags
- `Views/` contain no SQL or direct repository/service calls
- `Models/` contain only properties, constructors, and getters/setters

## Assignment 5 — Authentication

**Goal:** Add user registration, login, and logout, with passwords stored securely — never in plaintext.

### What was done

1. Created a `users` table (`id`, `username`, `password`, `created_at`, `updated_at`)
2. Built `App\Models\User` — a plain data object mirroring the `users` table, with getters/setters only
3. Built `App\Repositories\UserRepositoryInterface`, defining `findByUsername()` and `create()`
4. Built `App\Repositories\MySqlUserRepository`, implementing the interface with prepared statements
5. Built `App\Controllers\AuthController`:
   - `showLogin()` / `showRegister()` — render the login/registration forms via `View::render()`
   - `register()` — validates input (required fields, minimum password length, password confirmation match, username uniqueness), hashes the password with `password_hash()`, and saves the new user
   - `login()` — starts a session, looks up the user by username, verifies the password with `password_verify()`, and stores `$_SESSION['user_id']` on success
   - `logout()` — destroys the session and redirects to `/login`
6. Registered routes in `public/index.php`:

```php
$router->get('/login', AuthController::class . '@showLogin');
$router->post('/login', AuthController::class . '@login');
$router->get('/register', AuthController::class . '@showRegister');
$router->post('/register', AuthController::class . '@register');
$router->get('/logout', AuthController::class . '@logout');
```

7. Added `session_start()` to `public/index.php` so session data is available to every controller

### Validation

- Registering a new user and then inspecting the `users` table shows the `password` column holding a bcrypt-style hash (`$2y$10$...`), never the plaintext password
- Logging in with correct credentials sets `$_SESSION['user_id']` and redirects to `/books`
- Logging in with incorrect credentials shows "Invalid username or password." without revealing which field was wrong
- Registering with a duplicate username, a too-short password, or mismatched password confirmation is rejected with a specific message
- Visiting `/logout` clears the session and redirects to `/login`

## Assignment 6 — Session Management

**Goal:** Protect sensitive routes so only logged-in users can create, edit, or delete books. Guests can only view the book list.

### What was done

1. Added session initialization — session_start() is called at the very beginning of public/index.php before any output, ensuring $_SESSION is available throughout the request lifecycle
   - `Views/layouts/footer.php` — closes the container, loads Bootstrap's JS bundle
2. Implemented authentication middleware pattern — BookController now includes a private requireLogin() helper method that checks for $_SESSION['user_id'] before allowing any create/edit/delete operation:

```php

private function requireAuth(): void{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
```

### Validation

Open /books/create without logging in — you should be redirected to /login. After logging in, the same URL should display the create book form. Inspect the database to confirm passwords are stored as bcrypt hashes ($2y$10$...), not plain text.

## Assignment 7 — CRUD Interface with Bootstrap

**Goal:** Books List, Add Book, Edit Book, Delete Book, Book Details pages, styled with Bootstrap 5.

### What was done

1. Added Bootstrap 5 via CDN in a shared layout:
   - `Views/layouts/header.php` — doctype, Bootstrap CSS, a navbar (Books link, Log in/Log out depending on session state), opens the `.container`
   - `Views/layouts/footer.php` — closes the container, loads Bootstrap's JS bundle
2. Every view now `include`s the header/footer instead of repeating boilerplate HTML
3. Replaced plain HTML with Bootstrap components:
   - Book list → `table table-striped`, `btn`/`btn-sm` action buttons, `btn-success`/`btn-outline-*` variants
   - Forms (create/edit/login/register) → `form-control`, `mb-3`, `col-md-*` for width constraints
   - Success/error messages → `alert alert-success` / `alert-danger`
   - Book details → Bootstrap `card`
   - Homepage → Bootstrap jumbotron-style panel (`p-5 bg-light rounded-3`)

### Validation

All CRUD pages (list, create, edit, delete, show) render with Bootstrap styling and remain fully functional — no changes to controller logic, only to view markup.

## Assignment 8 — Search

**Goal:** Search books by Title, Author, or ISBN using GET requests, e.g. `/books?search=clean`.

### What was done

1. Added `search(string $query): array` to `BookRepositoryInterface` and implemented it in `MySqlBookRepository`:

```php
public function search(string $query): array
{
    $statement = $this->pdo->prepare(
        self::SELECT_BASE . " WHERE b.title LIKE :q OR a.name LIKE :q OR b.isbn LIKE :q"
    );
    $statement->execute(['q' => '%' . $query . '%']);
    return array_map([$this, 'hydrate'], $statement->fetchAll());
}
```

One query checks all three fields (title, author, ISBN) at once using `LIKE`, joined via the existing `authors` table.

2. Added `searchBooks(string $query): array` to `LibraryService`, delegating to the repository.
3. Updated `BookController::index()` to read `$_GET['search']`:

```php
$search = trim($_GET['search'] ?? '');
$books = $search !== '' ? $this->service->searchBooks($search) : $this->service->listBooks();
```

4. Added a GET search form to `Views/books/index.php`, pre-filled with the current query, plus a "Clear" link and a "Showing results for ..." message when a search is active.

### Validation

- `/books?search=clean` returns books matching "clean" in title, author, or ISBN
- `/books` (no search param) still shows the full list as before
- Searching for a non-matching term shows "No books found."

## Installation

**Requirements:** PHP 8.3+, Composer, MySQL

```bash
git clone <your-repo-url>
cd library-web
composer install
```

Import the schema (`books`, `authors`, `category`, `users` tables) into MySQL, then update the connection details in each controller's constructor to match your local setup.

```bash
php -S localhost:8000 -t public public/index.php
```

Then visit `http://localhost:8000`.

## Upcoming Work

- [ ] Assignment 9 — Pagination
- [ ] Assignment 10 — Custom 404/500 error pages

## Author

Ahmad Subhan