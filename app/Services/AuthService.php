<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService{

    public function __construct(private UserRepositoryInterface $userRepository)
    {}

public function register(array $data): array
    {
        // Extract interest IDs before creating user
        $interestIds = $data['interest_ids'] ?? [];
        unset($data['interest_ids']);

        // Create the user
        $user = $this->userRepository->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        // Attach interests if student
        if ($user->role === 'student' && !empty($interestIds)) {
            $this->userRepository->attachInterests($user->id, $interestIds);
        }

        // Generate JWT token
        $token = auth('api')->login($user);

        return $this->respondWithToken($user, $token);
    }

    public function login(array $credentials)
    {
        $token = auth('api')->attempt([
            'email'    => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        if(!$token){
            throw new Exception('Invalid credentials', 401);
        }

        $user = auth('api')->user();

        return $this->respondWithToken($user, $token);
    }

    public function logout()
    {
        auth('api')->logout();
    }

    public function refresh()
    {
        $token = auth('api')->refresh();
        $user = auth('api')->user();

        return $this->respondWithToken($user, $token);
    }

    // ─── Authenticated User ──────────────────────────────────
    public function me(): mixed
    {
        return auth('api')->user();
    }


    public function sendResetLink(string $email)
    {
        $status = Password::sendResetLink(['email'=> $email]);

        if($status !== Password::RESET_LINK_SENT) {
            throw new Exception(__($status),400);
        }

        return __($status);
    }

    public function resetPassword(array $data)
    {
        $status = Password::reset(
            [
                'email'                 => $data['email'],
                'password'              => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token'                 => $data['token'],
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60), 
                ])->save();

                event(new PasswordReset($user));
            }
        );
        if ($status !== Password::PASSWORD_RESET) {
            throw new Exception(__($status), 400);
        }

        return __($status);
    }


     // ─── Helper ──────────────────────────────────────────────
    private function respondWithToken($user, string $token): array
    {
        return [
            'user'         => $user->load('interests'),
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('jwt.ttl') * 60,
        ];
    }

}