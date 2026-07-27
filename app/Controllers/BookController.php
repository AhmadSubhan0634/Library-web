<?php

namespace App\Controllers;

use App\Services\LibraryService;
use App\Repositories\MySqlBookRepository;
use App\Core\Database;
use PDOException;

// Handles incoming requests related to books.
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

    // Display a list of all books
public function index(): void{
    if ($this->error) {
        echo $this->error;
        $this->disconnect();
        return;
    }

    $books = $this->service->listBooks();

    echo "<h1>Books</h1>";

    if (empty($books)) {
        echo "<p>No books found.</p>";
    } else {
        echo '<table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Category</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>';
        foreach ($books as $book) {
            echo '<tr>
                <td>' . htmlspecialchars($book->getTitle()) . '</td>
                <td>' . htmlspecialchars($book->getAuthor()) . '</td>
                <td>' . htmlspecialchars($book->getIsbn()) . '</td>
                <td>' . htmlspecialchars($book->getCategory()) . '</td>
                <td>' . htmlspecialchars($book->getYear()) . '</td>
                <td>
                    <a href="/books/show?isbn=' . urlencode($book->getIsbn()) . '">View</a>
                    <a href="/books/edit?isbn=' . urlencode($book->getIsbn()) . '">Edit</a>
                    <form method="POST" action="/books/delete" style="display: inline;">
                        <input type="hidden" name="isbn" value="' . htmlspecialchars($book->getIsbn()) . '">
                        <button type="submit" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>
                </td>
            </tr>';
        }
        echo '</table>';
    }
    
    $this->disconnect();
}

    // Display details of a specific book by ISBN
    public function show(): void{
        if ($this->error) {
            echo $this->error;
            $this->disconnect();
            return;
        }

        $isbn = $_GET['isbn'] ?? '';
        $book = $this->service->searchByisbn($isbn);

        if ($book === null) {
            echo "<p>No book found with ISBN '$isbn'.</p>";
            $this->disconnect();
            return;
        }

        echo "<h1>" . $book->getTitle() . "</h1>";
        echo "<p>Author: " . $book->getAuthor() . "</p>";
        echo "<p>ISBN: " . $book->getisbn() . "</p>";
        echo "<p>Category: " . $book->getCategory() . "</p>";
        echo "<p>Year: " . $book->getYear() . "</p>";
        
        $this->disconnect();
    }

    // Display the form to add a new book
    public function create(): void{
        if ($this->error) {
            echo $this->error;
            $this->disconnect();
            return;
        }

        echo "<h1>Add Book</h1>";
        echo '<form method="POST" action="/books">
                <input type="text" name="title" placeholder="Title"><br>
                <input type="text" name="author" placeholder="Author"><br>
                <input type="text" name="isbn" placeholder="ISBN"><br>
                <input type="text" name="category" placeholder="Category"><br>
                <input type="text" name="year" placeholder="Year"><br>
                <button type="submit">Add Book</button>
              </form>';
        
        $this->disconnect();
    }

    // Process and store a newly created book
    public function store(): void{
        if ($this->error) {
            echo $this->error;
            $this->disconnect();
            return;
        }

        $title    = $_POST['title'] ?? '';
        $author   = $_POST['author'] ?? '';
        $isbn     = $_POST['isbn'] ?? '';
        $category = $_POST['category'] ?? '';
        $year     = (int)($_POST['year'] ?? 0);

        $message = $this->service->addBook($title, $author, $isbn, $category, $year);
        echo "<p>$message</p>";
        
        $this->disconnect();
    }

    // Display the form to edit an existing book
    public function edit(): void{
        if ($this->error) {
            echo $this->error;
            $this->disconnect();
            return;
        }

        $isbn = $_GET['isbn'] ?? '';
        $book = $this->service->searchByisbn($isbn);

        if ($book === null) {
            echo "<p>No book found with ISBN '$isbn'.</p>";
            $this->disconnect();
            return;
        }

        echo "<h1>Edit Book</h1>";
        echo '<form method="POST" action="/books/update">
                <input type="hidden" name="isbn" value="' . $book->getisbn() . '">
                <input type="text" name="title" value="' . $book->getTitle() . '"><br>
                <input type="text" name="author" value="' . $book->getAuthor() . '"><br>
                <input type="text" name="category" value="' . $book->getCategory() . '"><br>
                <input type="text" name="year" value="' . $book->getYear() . '"><br>
                <button type="submit">Update Book</button>
              </form>';
        
        $this->disconnect();
    }

    // Process and update an existing book
    public function update(): void{
        if ($this->error) {
            echo $this->error;
            $this->disconnect();
            return;
        }

        $isbn = $_POST['isbn'] ?? '';
        $data = [
            'title'    => $_POST['title'] ?? '',
            'author'   => $_POST['author'] ?? '',
            'category' => $_POST['category'] ?? '',
            'year'     => $_POST['year'] ?? '',
        ];

        $message = $this->service->updateBook($isbn, $data);
        echo "<p>$message</p>";
        
        $this->disconnect();
    }

    // Delete a book by ISBN
    public function destroy(): void{
        if ($this->error) {
            echo $this->error;
            $this->disconnect();
            return;
        }

        $isbn = $_POST['isbn'] ?? '';
        $message = $this->service->deleteBook($isbn);
        echo "<p>$message</p>";
        
        $this->disconnect();
    }

    // Disconnect database connection
    public function disconnect(): void{
        if ($this->database !== null) {
            $this->database->disconnect();
            $this->database = null; 
        }
    }
}