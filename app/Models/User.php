<?php

namespace App\Models;

class User{
    private int $id;
    private string $username,$passwordHash;

    // Default constructor
    public function __construct(int $id=0,string $username="",string $password=""){
        $this->id=$id;
        $this->username=$username;
        $this->passwordHash=$password;
    }

     // Getters
    public function getid(): int { return $this->id; }
    public function getusername(): string { return $this->username; }
    public function getpassword(): string { return $this->passwordHash; }
  
    // Setters
    public function setid(string $id): void { $this->id = $id; }
    public function setusername(string $username): void { $this->username = $username; }
    public function setpassword(string $password): void { $this->passwordHash = $password; } 

    }