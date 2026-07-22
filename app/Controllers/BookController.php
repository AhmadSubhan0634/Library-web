<?php

namespace App\Controllers;

use App\Services\LibraryService;
use App\Repositories\JsonBookRepository;

// Handles incoming requests related to books.
class BookController{
    private LibraryService $service;

    public function __construct(){

        $repository = new JsonBookRepository(__DIR__ . '/../../storage/books.json');
        $this->service = new LibraryService($repository);
    }

    public function index(): void{
        $books = $this->service->listBooks();

        echo "<h1>Books</h1>";

        // Checks if no book exist
        if (empty($books)) {
            echo "<p>No books found.</p>";
            return;
        }

        // Prints all the books
        foreach ($books as $book) {
            echo "<p>" . $book->getTitle() . " by " . $book->getAuthor()
                . " (ISBN: " . $book->getIsbn() . ") — "
                . $book->getCategory() . ", " . $book->getYear() . "</p>";
        }
    }
}