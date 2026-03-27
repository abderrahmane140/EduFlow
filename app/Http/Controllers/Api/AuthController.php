<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {}

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'role'         => 'required|in:student,teacher',
            'interest_ids' => 'array',
            'interest_ids.*' => 'exists:interests,id',
        ]);

        try{
            $result = $this->authService->register($validated);
            return response()->json($result,201);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()],500 );
        }
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try{
            $result = $this->authService->login($validated);
            return response()->json($result);
        }catch(Exception $e){
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }

    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        try{
            $result = $this->authService->refresh();
            return response()->json($result);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }


    public function me()
    {
        return response()->json($this->authService->me());
    }

    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $message = $this->authService->sendResetLink($request->email);
            return response()->json(['message' => $message]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()],(int) $e->getCode() ?: 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

         try {
            $message = $this->authService->resetPassword($validated);
            return response()->json(['message' => $message]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], (int) $e->getCode() ?: 500);
        }
    }
}
