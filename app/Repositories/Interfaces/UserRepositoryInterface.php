<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User; // create new user
    public function findByEmail(string $email): ?User; // find user by email
}
