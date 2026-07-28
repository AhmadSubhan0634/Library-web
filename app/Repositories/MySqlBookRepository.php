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

    // Searches across title, author name, and ISBN in one query.
    public function search(string $query): array{
        $statement = $this->pdo->prepare(
            self::SELECT_BASE . " WHERE b.title LIKE :title OR a.name LIKE :author OR b.isbn LIKE :isbn"
        );
        $like = '%' . $query . '%';
        $statement->execute([
            'title'  => $like,
            'author' => $like,
            'isbn'   => $like,
        ]);

        return array_map([$this, 'hydrate'], $statement->fetchAll());
    }

    public function getPage(int $page, int $perPage, string $search = ''): array{
        $offset = ($page - 1) * $perPage;

        if ($search !== '') {
            $sql = self::SELECT_BASE . " WHERE b.title LIKE :title OR a.name LIKE :author OR b.isbn LIKE :isbn
                ORDER BY b.title,b.isbn LIMIT :limit OFFSET :offset";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':title', '%' . $search . '%');
            $statement->bindValue(':author', '%' . $search . '%');
            $statement->bindValue(':isbn', '%' . $search . '%');
        } else {
            $sql = self::SELECT_BASE . " ORDER BY b.title,b.isbn LIMIT :limit OFFSET :offset";
            $statement = $this->pdo->prepare($sql);
        }

        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'hydrate'], $statement->fetchAll());
    }

    // Total matching rows, needed to calculate how many pages exist.
    public function countAll(string $search = ''): int{
        if ($search !== '') {
            $sql = "SELECT COUNT(*) as total FROM books b
                LEFT JOIN authors a ON b.author_id = a.id
                WHERE b.title LIKE :title OR a.name LIKE :author OR b.isbn LIKE :isbn";
            $statement = $this->pdo->prepare($sql);
            $like = '%' . $search . '%';
            $statement->execute([
                'title'  => $like,
                'author' => $like,
                'isbn'   => $like,
            ]);
        } else {
            $statement = $this->pdo->query("SELECT COUNT(*) as total FROM books");
        }

        $row = $statement->fetch();
        return (int)($row['total'] ?? 0);
    }  
}