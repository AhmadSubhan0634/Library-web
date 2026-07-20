# Library Web

A browser-based Library Management System built as a mini MVC framework **without** using Laravel, Symfony, or any other framework — built from scratch to understand what a framework actually does under the hood.

> **Status:** Assignment 1 complete (Composer Autoloading). Assignments 2–10 are not yet implemented.

## Tech Stack

- PHP 8.3+
- Composer
- MySQL (not yet connected — Assignment 3)
- PDO (not yet implemented — Assignment 3)
- Apache / PHP built-in server
- Bootstrap 5 (not yet added — Assignment 7)

## Project Structure

```
library-web/
├── app/
│   ├── Controllers/     # HomeController, BookController, AuthController
│   ├── Models/          # Book, User
│   ├── Services/        # (empty — reserved for business logic)
│   ├── Repositories/    # (empty — reserved for data access layer)
│   ├── Views/           # layouts/, books/, auth/, home/
│   └── Core/            # Router, Database, Request, Response, View
├── public/
│   └── index.php        # Application entry point
├── composer.json
├── .gitignore
└── README.md
```

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

3. Created placeholder classes across `Controllers/`, `Models/`, and `Core/` (empty class bodies for now — real logic comes in later assignments) to confirm the autoloader resolves every namespace correctly.
4. Ran `composer dump-autoload` to generate the autoloader.

### Validation

`public/index.php` requires **only** the Composer autoloader — no other file in the project uses `require_once`:

```php
require __DIR__ . '/../vendor/autoload.php';
```

Running the entry point confirms every class loads correctly purely through its namespace:

```
Composer autoloading check:

App\Core\Router ... OK
App\Core\Database ... OK
App\Core\Request ... OK
App\Core\Response ... OK
App\Core\View ... OK
App\Controllers\HomeController ... OK
App\Controllers\BookController ... OK
App\Controllers\AuthController ... OK
App\Models\Book ... OK
App\Models\User ... OK
```

## Setup

```bash
git clone <your-repo-url>
cd library-web
composer install
php public/index.php
```

## Upcoming Work

- [ ] Assignment 2 — Router
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