# Library Web

A browser-based Library Management System built as a mini MVC framework **without** using Laravel, Symfony, or any other framework — built from scratch to understand what a framework actually does under the hood.

> **Status:** Assignments 1–10 complete (Composer Autoloading, Router, PDO Database Layer, MVC/View separation, Authentication, Session Management, CRUD Interface, Search, Pagination, Error pages).

## Project Overview

This project reimplements a console-based Library System as a real browser-based web application. The goal isn't to build "another CRUD app" — it's to understand what tools like Laravel actually do internally by building the equivalent pieces manually:

- How an HTTP request travels from browser to server and back
- How routing decides which code runs for a given URL
- How Composer's autoloader replaces dozens of manual `require` statements
- How MVC separates data (Models), display (Views), and decision-making (Controllers)
- How a database layer, authentication, and sessions fit into all of this

The finished app lets users browse, search, add, edit, and delete books through a proper web UI backed by MySQL — with every layer built by hand first.

## Tech Stack

- PHP 8.3+
- Composer
- MySQL
- PDO
- Apache / PHP built-in server
- Bootstrap 5

## Architecture

Browser
│
v
public/index.php (front controller — the single entry point for every request)
│
v
Core\Router (matches HTTP method + URI to a Controller action)
│
v
Controllers (receive the request, delegate to a Service/Repository, render a View)
│
v
Services / Repositories (business logic + data access — MySQL via PDO)
│
v
Models (plain data objects, e.g. Book, User)

Controllers also hand data off sideways to:
Core\View (renders a template from app/Views/ and returns/echoes HTML)


## Folder Structure

library-web/
├── app/
│ ├── Controllers/ # HomeController, BookController, AuthController
│ ├── Models/ # Book, User
│ ├── Services/ # LibraryService
│ ├── Repositories/ # BookRepositoryInterface, MySqlBookRepository, BookMapper,
│ │ # UserRepositoryInterface, MySqlUserRepository
│ ├── Views/
│ │ ├── layouts/ # Shared page structure (header/footer wrapper)
│ │ ├── books/ # index, show, create, edit, store, update, delete
│ │ ├── auth/ # login, register
│ │ ├── home/ # index
│ │ └── errors/ # error.php, 404.php, 500.php
│ └── Core/ # Router, Database, Request, Response, View
├── public/
│ └── index.php # Application entry point
├── database/
│ └── schema.sql # books, authors, category, users tables
├── composer.json
├── .gitignore
└── README.md


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
- **Service layer (`LibraryService`) between Controllers and Repositories** — business rules (e.g. rejecting a duplicate ISBN) live in one place, not scattered across controllers.
- **Prepared statements throughout** — every SQL query in `MySqlBookRepository` and `MySqlUserRepository` uses bound parameters, avoiding SQL injection.
- **Normalized schema with `findOrCreate` helpers** — authors and categories live in their own tables, referenced from `books` via foreign keys, avoiding duplicate author/category rows.
- **Controllers hand data to Views instead of echoing HTML** — `Core\View` centralizes template rendering (Assignment 4), so every controller action just prepares data and calls one method rather than building markup inline.
- **`::class` + string method dispatch** — mirrors the "Controller@method" convention used by Laravel, without needing its full routing engine.
- **Passwords are never stored or compared in plaintext** — `password_hash()` on registration, `password_verify()` on login, per Assignment 5's requirement.
- **Session-based authentication (`$_SESSION['user_id']`)** — a single `requireLogin()` guard, checked at the top of every state-changing book method (create/store/edit/update/destroy) in Assignment 6, keeps guests able to view and search while restricting mutation.
- **Bootstrap via CDN, not a build pipeline** — kept the project dependency-free (no npm/webpack) for Assignment 7's styling; a shared `layouts/header.php`/`footer.php` avoids repeating the CDN link and navbar markup in every view.
- **Search and pagination share one query path** — `search()` (Assignment 8) and `getPage()`/`countAll()` (Assignment 9) both operate on the same `title`/`author`/`isbn` `LIKE` condition, so searched results paginate correctly instead of being two diverging code paths.
- **Graceful failure over crashes** — the Router checks `class_exists()`/`method_exists()` before dispatching, wraps controller execution in a `try/catch` for both `PDOException` and a catch-all `\Throwable`, and returns proper HTTP status codes (`404`/`500`) with styled error pages instead of raw PHP crashes (Assignment 10).

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

- `Controllers/` contain no HTML tags
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
   - `login()` — looks up the user by username, verifies the password with `password_verify()`, and stores `$_SESSION['user_id']` on success
   - `logout()` — destroys the session and redirects to `/login`
6. Registered routes in `public/index.php`:

```php
$router->get('/login', AuthController::class . '@showLogin');
$router->post('/login', AuthController::class . '@login');
$router->get('/register', AuthController::class . '@showRegister');
$router->post('/register', AuthController::class . '@register');
$router->get('/logout', AuthController::class . '@logout');
```

7. Added `session_start()` to `public/index.php` (before any output) so session data is available to every controller

### Validation

- Registering a new user and then inspecting the `users` table shows the `password` column holding a bcrypt-style hash (`$2y$10$...`), never the plaintext password
- Logging in with correct credentials sets `$_SESSION['user_id']` and redirects to `/books`
- Logging in with incorrect credentials shows "Invalid username or password." without revealing which field was wrong
- Registering with a duplicate username, a too-short password, or mismatched password confirmation is rejected with a specific message
- Visiting `/logout` clears the session and redirects to `/login`

## Assignment 6 — Session Management

**Goal:** Protect sensitive routes so only logged-in users can create, edit, or delete books. Guests can only view the book list.

### What was done

1. `session_start()` is called at the very beginning of `public/index.php`, before any output, ensuring `$_SESSION` is available throughout the request lifecycle
2. Added a private `requireLogin()` guard to `BookController`, called as the first line of `create()`, `store()`, `edit()`, `update()`, and `destroy()`:

```php
private function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}
```

3. Updated `Views/books/index.php` to hide the "Add New Book" link and each row's Edit/Delete controls unless a session exists — guests see a "Log in to add or edit books" prompt instead. "View" remains visible to everyone.

### Validation

- Opening `/books/create` without logging in redirects to `/login`; after logging in, the same URL displays the create-book form
- Logged out, `/books` shows only View links — no Add/Edit/Delete controls
- Inspecting the database confirms passwords are stored as bcrypt hashes (`$2y$10$...`), not plain text

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

## Assignment 9 — Pagination

**Goal:** Display 10 books per page, with Previous / page numbers / Next navigation.

### What was done

1. Added `getPage(int $page, int $perPage, string $search = '')` and `countAll(string $search = '')` to `BookRepositoryInterface`, implemented in `MySqlBookRepository`:
   - `getPage()` uses `LIMIT`/`OFFSET` (bound as integers) to fetch one page, honoring the Assignment 8 search term if present, ordered by title for stable results across pages
   - `countAll()` returns the total number of matching rows, used to calculate page count
2. Added `listBooksPaginated(int $page, int $perPage, string $search = '')` to `LibraryService`, returning the page's books plus `totalPages`/`currentPage`
3. `BookController::index()` now reads `$_GET['page']` (default 1) alongside `$_GET['search']`, fixed at 10 books per page
4. Added Bootstrap pagination markup (`Views/books/index.php`) — Previous / numbered pages / Next, each link preserving the current search term, with the current page marked `active` and out-of-range Previous/Next disabled

### Validation

- With more than 10 books, `/books` shows only the first 10, with page links below
- `/books?page=2` shows the next 10
- Searching and paginating together (`/books?search=clean&page=1`) work correctly — page links keep the search term attached
- With fewer than 11 total books, no pagination controls are shown at all

## Assignment 10 — Custom Error Pages

**Goal:** Custom 404 and 500 pages instead of raw PHP errors.

### What was done

1. Added `Views/errors/404.php` and `Views/errors/500.php`, both using the shared Bootstrap layout (`layouts/header.php` / `footer.php`) for a consistent look with the rest of the app
2. Updated `App\Core\Router::resolve()`:
   - Unmatched routes now call `View::render('errors/404')` instead of `echo "404 - Route not found"`
   - Malformed actions / missing controller-method pairs render `errors/500` with a relevant message
   - Database connection failures (caught `PDOException`) render `errors/500` with "Database connection failed."
   - Added a catch-all `\Throwable` handler so any other unexpected error also renders the 500 page instead of a raw PHP stack trace, while still setting the correct HTTP status code

### Validation

- Visiting an unregistered path (e.g. `/nonexistent-page`) shows the styled 404 page, with a real `404` HTTP status code
- Simulating a database failure (invalid credentials) shows the styled 500 page with "Database connection failed.", with a real `500` HTTP status code
- No raw PHP error output or stack traces are ever shown to the user

## Installation

**Requirements:** PHP 8.3+, Composer, MySQL

```bash
git clone <your-repo-url>
cd library-web
composer install
```

Import the schema (`books`, `authors`, `category`, `users` tables) from `database/schema.sql` into MySQL, then update the connection details in `BookController`'s and `AuthController`'s constructors to match your local setup.

```bash
php -S localhost:8000 -t public
```

Then visit `http://localhost:8000`.

## Author

Ahmad Subhan
