<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param array<string, mixed> $credentials
     * @return array<string, mixed>
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        return [
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $user->createToken('api-token')->plainTextToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'balance' => (float) $user->balance,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function logout(User $user): array
    {
        return [
            'success' => false,
            'message' => 'Logout logic is not implemented yet.',
        ];
    }
}
