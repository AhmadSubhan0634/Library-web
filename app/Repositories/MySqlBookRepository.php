<?php

namespace App\Repositories;

use App\Repositories\BookRepositoryInterface;
use App\Core\Database;
use App\Models\Book;
use PDO;

class MySqlBookRepository implements BookRepositoryInterface{
    private PDO $pdo;
    private const SELECT_BASE="select b.title,b.isbn,YEAR(b.published_year) as pub_year,a.name as author_name,c.name as category_name
    from books b left join authors a on b.author_id=a.id
    left join category c on b.category_id=c.id";

    public function __construct(Database $database){
        $this->pdo=$database->getConnection();
    }

    public function getAll(): array{
        $statement=$this->pdo->query(self::SELECT_BASE);
        return array_map([$this,'hydrate'],$statement->fetchAll());
    }

    public function findByIsbn(string $isbn):?Book{
        $statement=$this->pdo->prepare(self::SELECT_BASE." where b.isbn=:isbn");
        $statement->execute(['isbn'=>$isbn]);
        $row=$statement->fetch();
        return $row?$this->hydrate($row):null;
    }

    public function findByTitle(string $title): array
    {
        $statement = $this->pdo->prepare(self::SELECT_BASE . " WHERE b.title LIKE :title");
        $statement->execute(['title' => '%' . $title . '%']);

        return array_map([$this, 'hydrate'], $statement->fetchAll());
    }

    public function delete(string $isbn): bool{
        $statement=$this->pdo->prepare("delete from books where isbn=:isbn");
        $statement->execute(['isbn'=> $isbn]);
        return $statement->rowCount()>0;
    }

    private function findOrCreateAuthor(string $name):int {
        $statement=$this->pdo->prepare("select id from authors where name= :name");
        $statement->execute(['name'=>$name]);
        $row=$statement->fetch();

        if($row){
            return (int)$row['id'];
        }

        $email = strtolower(preg_replace('/[^a-z0-9]+/i', '.', trim($name))) . '@gmail.com';
        $insert=$this->pdo->prepare("insert into authors(name,email,country,created_at,updated_at) values (:name,:email,:country,CURDATE(),CURDATE())");
        $insert->execute(['name'=>$name,'email'=>$email,'country'=>'Pakistan',]);
        return (int)$this->pdo->lastInsertId();
    }

    private function findOrCreateCategory(string $name): int{
        $statement=$this->pdo->prepare("select id from category where name= :name");
        $statement->execute(['name'=>$name]);
        $row=$statement->fetch();

        if($row){
            return (int)$row['id'];
        }

        $insert=$this->pdo->prepare(" insert into category (name, description, created_at, updated_at)values (:name, :description, CURDATE(), CURDATE())");
        $insert->execute(['name'=>$name,'description'=>',']);

        return (int)$this->pdo->lastInsertId();
    }

    private function yearToDate(int $year):string{
        return sprintf('%04d-01-01',$year);
    }

    public function save(Book $book): void{
        $author_id=$this->findOrCreateAuthor($book->getAuthor());
        $category_id=$this->findOrCreateCategory($book->getCategory());
        $statement=$this->pdo->prepare("insert into books (title, isbn, published_year, author_id, category_id, created_at, updated_at) values (:title, :isbn, :published_year, :author_id, :category_id, CURDATE(), CURDATE())");

        $statement->execute(['title'=>$book->getTitle(),'isbn'=>$book->getIsbn(),'published_year'=>$this->yearToDate($book->getYear()),'author_id'=>$author_id,'category_id'=>$category_id,]);
    }

    public function update(Book $book): bool{
        $author_id = $this->findOrCreateAuthor($book->getAuthor());
        $category_id = $this->findOrCreateCategory($book->getCategory());

        $statement = $this->pdo->prepare(
            "UPDATE books SET title = :title, author_id = :author_id, category_id = :category_id, published_year = :published_year, updated_at = CURDATE() WHERE isbn = :isbn"
        );
        $statement->execute([
            'title' => $book->getTitle(),
            'author_id' => $author_id,
            'category_id' => $category_id,
            'published_year' => $this->yearToDate($book->getYear()),
            'isbn' => $book->getIsbn(),
        ]);

        return $statement->rowCount() > 0;
    }

    public function hydrate(array $row): Book{
        $year=$row['pub_year']?(int)$row['pub_year']:0;
        return new Book($row['title'],$row['author_name']??'',$row['isbn'],$row['category_name']??'',$year);
    }
}