# Library Web

A browser-based Library Management System being built as a mini MVC framework **without** using Laravel, Symfony, or any other framework — built from scratch to understand what a framework actually does under the hood.

> **Status:** Assignment 1 complete (Composer Autoloading). Assignments 2–10 are not yet implemented.

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

## Folder Structure

```
library-web/
├── app/
│   ├── Controllers/     # HomeController, BookController, AuthController
│   ├── Models/          # Book, User
│   ├── Services/        # (empty for now)
│   ├── Repositories/    # (empty for now)
│   ├── Views/           # layouts/, books/, auth/, home/
│   └── Core/            # Router, Database, Request, Response, View
├── public/
│   └── index.php        # Application entry point
├── composer.json
├── .gitignore
└── README.md
```

| Folder | Responsibility |
|---|---|
| `Controllers/` | Receive requests, call Services, return a response |
| `Models/` | Plain data objects — no logic beyond getters/setters |
| `Services/` | Business logic and validation rules (not yet implemented) |
| `Repositories/` | Data access layer (not yet implemented) |
| `Views/` | HTML templates (not yet used — Assignment 4) |
| `Core/` | Framework-level building blocks: routing, database connection, request/response wrappers |

## Design Decisions

- **PSR-4 autoloading over `require_once`** — every class is loaded automatically based on its namespace, matching how real-world PHP projects (and Composer packages) work.
- **Namespace mirrors folder structure** — `App\Controllers\BookController` lives at `app/Controllers/BookController.php`, and so on for every class. This is what lets Composer's autoloader find any class without being told where it lives.

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

No file in the project uses `require_once` — every class loads purely through its namespace via Composer's autoloader. The only `require` in the entire project is a single line in `public/index.php` that loads Composer's autoloader itself:

```php
require __DIR__ . '/../vendor/autoload.php';
```

At the time Assignment 1 was completed, a temporary test script in `public/index.php` confirmed every placeholder class resolved correctly with no manual `require` statements needed. (This test script has since been replaced by the real application entry point built in later assignments.)

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

- [ ] Assignment 2 — Simple Router
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
