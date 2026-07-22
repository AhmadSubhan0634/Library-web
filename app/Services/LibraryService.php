<?php

namespace App\Services;

use App\Repositories\BookRepositoryInterface;
use App\Models\Book;

class LibraryService{
    private BookRepositoryInterface $repo;

    public function __construct(BookRepositoryInterface $repo){
        $this->repo = $repo;
    }

    public function addBook(string $title, string $author, string $isbn, string $category, int $year): string{
        if ($this->repo->findByIsbn($isbn) !== null) {
            return " A book with ISBN '$isbn' already exists.";
        }
        $book = new Book($title, $author, $isbn, $category, $year);
        $this->repo->save($book);
        return "Book '$title' added successfully.";
    }

    public function listBooks(): array
    {
        return $this->repo->getAll();
    }

    public function searchByTitle(string $title): array
    {
        return $this->repo->findByTitle($title);
    }

    public function searchByIsbn(string $isbn): ?Book
    {
        return $this->repo->findByIsbn($isbn);
    }

    public function updateBook(string $isbn, array $data): string{
        $book = $this->repo->findByIsbn($isbn);
        if ($book === null) {
            return "No book found with isbn '$isbn'.";
        }

        if (!empty($data['title'])){ $book->setTitle($data['title']); }
        if (!empty($data['author'])){ $book->setAuthor($data['author']); }
        if (!empty($data['category'])) { $book->setCategory($data['category']); }
        if (!empty($data['year'])){ $book->setYear((int) $data['year']); }

        $updated = $this->repo->update($book);
        return $updated
            ? "Book updated successfully."
            : "Error: No book found with ISBN '$isbn'.";
    }

    public function deleteBook(string $isbn): string
    {
        $deleted = $this->repo->delete($isbn);
        return $deleted
            ? "Book deleted successfully."
            : "Error: No book found with ISBN '$isbn'.";
    }
}