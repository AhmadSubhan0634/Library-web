<?php

namespace App\Controllers;

use App\Repositories\MySqlUserRepository;
use App\Core\Database;
use App\Core\View;
use PDOException;

class AuthController{
    private ?Database $database = null;
    private ?string $error;
    private MySqlUserRepository $repository;  

    public function __construct(){
        try{
            $this->database = new Database('localhost','Library_System','root','F240634@lhr.nu');
            $this->repository = new MySqlUserRepository($this->database);  
            $this->error = NULL;
        }
        catch (PDOException $e) {
            $this->error = "Database connection failed: " . $e->getMessage();
        }
    }

    public function showLogin(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        try {
            View::render('auth/login');
        } catch (\RuntimeException $e) {
            $this->showError("View error: " . $e->getMessage());
            $this->disconnect();
        }
    }

    public function login(): void{
        session_start(); // Start session

        if($this->error){
            $this->showError($this->error);
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->repository->findByUsername($username);

        if(!$user){
            $this->showError("Invalid username or password.");
            return;
        }

        if(!password_verify($password, $user->getPasswordHash())){
            $this->showError("Invalid username or password.");
            return;
        }

        $_SESSION['user_id'] = $user->getId();
        header('Location: /books');
        exit;
    }

    public function logout(): void{
    session_start();
    session_destroy();
    header('Location: /login');
    exit;
    }

    public function showRegister(): void{
        if ($this->error) {
            $this->showError($this->error);
            return;
        }

        try {
            View::render('auth/register');
        } catch (\RuntimeException $e) {
            $this->showError("View error: " . $e->getMessage());
            $this->disconnect();
        }
    }

    public function register(): void{
        if($this->error){
            $this->showError($this->error);
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if($username === '' || $password === ''){
            $this->showError("Username and password are required.");
            return;
        }

        if(strlen($password) < 8){
            $this->showError("Password must be at least 8 characters long.");
            return;
        }

        if($password !== $confirmPassword){
            $this->showError("Passwords do not match.");
            return;
        }

        $existingUser = $this->repository->findByUsername($username);

        if($existingUser){
            $this->showError("That username is already taken.");
            return;
        }

        // Never store the plaintext password.
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->repository->create($username, $passwordHash);

        header('Location: /login');
        exit;
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