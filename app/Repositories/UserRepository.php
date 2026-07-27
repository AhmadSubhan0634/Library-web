<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface {
    public function findByUsername(string $username): ?User;
    public function create(string $username, string $passwordHash): User;
}