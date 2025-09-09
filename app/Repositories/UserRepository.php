<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    // Create a new user in database
    public function create(array $data): User
    {
        return User::create($data);
    }

    // Find user by email (or null if not exists)
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
