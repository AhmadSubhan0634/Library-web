<?php

namespace App\Repositories;

use App\Models\User;
use App\Core\Database;
use PDO;

class MySqlUserRepository implements UserRepositoryInterface {

    private PDO $pdo;
    private const SELECT_BASE = "SELECT id, username, password, created_at, updated_at FROM users";

    public function __construct(Database $database){
        $this->pdo = $database->getConnection();
    }

    public function findByUsername(string $username): ?User{
        $statement = $this->pdo->prepare(self::SELECT_BASE . " WHERE username = :username");
        $statement->execute(['username' => $username]);
        $row = $statement->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(string $username, string $passwordHash): User{
        $statement = $this->pdo->prepare(
            "INSERT INTO users (username, password, created_at, updated_at)
             VALUES (:username, :password, NOW(), NOW())"
        );

        $statement->execute([
            'username' => $username,
            'password' => $passwordHash,
        ]);

        $id = (int)$this->pdo->lastInsertId();

        // Re-fetch so created_at/updated_at reflect the actual DB-generated values.
        $user = $this->findByUsername($username);

        return $user ?? new User($id, $username, $passwordHash, '', '');
    }

    private function hydrate(array $row): User{
        return new User(
            (int)$row['id'],
            $row['username'],
            $row['password'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}