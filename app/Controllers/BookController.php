<?php

namespace App\Controllers;

use App\Services\LibraryService;
use App\Repositories\MySqlBookRepository;
use App\Core\Database;
use App\Core\View;
use PDOException;

class BookController{
    private ?Database $database = null;
    private LibraryService $service;
    private ?string $error;
    private MySqlBookRepository $repository;  

    public function __construct(){
        try{
            $this->database = new Database('localhost','Library_System','root','F240634@lhr.nu');
            $this->repository = new MySqlBookRepository($this->database);  
            $this->service = new LibraryService($this->repository); 
            $this->error = NULL;
        }
        catch (PDOException $e) {
            $this->error = "Database connection failed: " . $e->getMessage();
        }
    }

    private function requireLogin(): void{
    if(!isset($_SESSION['user_id'])){
        header('Location: /login');
        exit();
    }
    }   

    public function index(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        try {
            $books = $this->service->listBooks();
            View::render('books/index', ['books' => $books]);
        } catch (\RuntimeException $e) {
            $this->showError("View error: " . $e->getMessage());
        }
        
        $this->disconnect();
    }

    public function show(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        $isbn = $_GET['isbn'] ?? '';
        $book = $this->service->searchByIsbn($isbn);

        if ($book === null) {
            $this->showError("No book found with ISBN '$isbn'.");
            return;
        }

        View::render('books/show', ['book' => $book]);
        $this->disconnect();
    }

    public function create(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        $this->requireLogin();

        View::render('books/create');
        $this->disconnect();
    }

    public function store(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        $this->requireLogin();

        $title    = $_POST['title'] ?? '';
        $author   = $_POST['author'] ?? '';
        $isbn     = $_POST['isbn'] ?? '';
        $category = $_POST['category'] ?? '';
        $year     = (int)($_POST['year'] ?? 0);

        $message = $this->service->addBook($title, $author, $isbn, $category, $year);
        View::render('books/store', ['message' => $message]);
        $this->disconnect();
    }

    public function edit(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        $this->requireLogin();

        $isbn = $_GET['isbn'] ?? '';
        $book = $this->service->searchByIsbn($isbn);

        if ($book === null) {
            $this->showError("No book found with ISBN '$isbn'.");
            return;
        }

        View::render('books/edit', ['book' => $book]);
        $this->disconnect();
    }

    public function update(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        $this->requireLogin();

        $isbn = $_POST['isbn'] ?? '';
        $data = [
            'title'    => $_POST['title'] ?? '',
            'author'   => $_POST['author'] ?? '',
            'category' => $_POST['category'] ?? '',
            'year'     => $_POST['year'] ?? '',
        ];

        $message = $this->service->updateBook($isbn, $data);
        View::render('books/update', ['message' => $message, 'isbn' => $isbn]);
        $this->disconnect();
    }

    public function destroy(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        $this->requireLogin();

        $isbn = $_POST['isbn'] ?? '';
        $message = $this->service->deleteBook($isbn);
        View::render('books/delete', ['message' => $message]);
        $this->disconnect();
    }

    private function showError(string $errorMessage): void{
    View::render('errors/error', ['message' => $errorMessage]);
    $this->disconnect();
    }

    public function disconnect(): void{
        if ($this->database !== null) {
            $this->database->disconnect();
            $this->database = null; 
        }
    }
}