<?php

namespace App\Repositories;

use App\Models\Book;

class BookMapper{
    public static function toArray(Book $book): array{
        return [
            'title'=>$book->getTitle(),
            'author'=>$book->getAuthor(),
            'isbn'=>$book->getisbn(),
            'category'=>$book->getCategory(),
            'year'=>$book->getYear(),
        ];
    }

    public static function fromArray(array $data): Book{
        return new Book(
            $data['title'],
            $data['author'],
            $data['isbn'],
            $data['category'],
            (int) $data['year']
        );
    }
}

