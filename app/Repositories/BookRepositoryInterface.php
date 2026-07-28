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
    public function search(string $query): array;
    public function getPage(int $page, int $perPage, string $search = ''): array;
    public function countAll(string $search = ''): int;


}
