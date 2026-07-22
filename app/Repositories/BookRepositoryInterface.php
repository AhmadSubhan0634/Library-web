<?php

namespace App\Repositories;

use App\Models\Book;

interface BookRepositoryInterface {
    public function getAll(): array;
    public function findByIsbn(string $isbn): ?Book;
    public function findByTitle(string $title): array;
    public function save(Book $book): void;
    public function update(Book $book): bool;
    public function delete(string $isbn): bool;
}