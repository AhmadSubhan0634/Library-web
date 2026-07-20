<?php

namespace App\Entities;

class Book{
    private string $title;
    private string $author;
    private string $isbn;
    private string $category;
    private int $year;

    // Default constructor
    public function __construct(string $title = "",string $author = "",string $isbn = "",string $category = "",int $year = 0) {
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
        $this->category = $category;
        $this->year = $year;
    }

    // Getters
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getisbn(): string { return $this->isbn; }
    public function getCategory(): string { return $this->category; }
    public function getYear(): int { return $this->year; }

    // Setters
    public function setTitle(string $title): void { $this->title = $title; }
    public function setAuthor(string $author): void { $this->author = $author; }
    public function setisbn(string $isbn): void { $this->isbn = $isbn; }
    public function setCategory(string $category): void { $this->category = $category; }
    public function setYear(int $year): void { $this->year = $year; }
}
