<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository; // inject repository
    }

    // Register a new user
    public function register(array $data): array
    {
        // Hash password before saving
        $data['password'] = Hash::make($data['password']);

        // Save using repository
        $user = $this->userRepository->create($data);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response data
        return [
            'message' => 'Registered Successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }

    // Login user
    public function login(array $credentials): ?array
    {
        // Find user by email
        $user = $this->userRepository->findByEmail($credentials['email']);

        // If user not found OR password mismatch → return null
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Login Successfull',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }
}
