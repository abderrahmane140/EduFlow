<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(
        private WishlistService $wishlistService
    ) {}

    // GET /api/student/wishlist
    public function index(): JsonResponse
    {
        return response()->json($this->wishlistService->getAll());
    }

    // POST /api/student/wishlist/{courseId}
    public function store(string $courseId): JsonResponse
    {
        try {
            $result = $this->wishlistService->save((int) $courseId);
            return response()->json($result, 201);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }

    // DELETE /api/student/wishlist/{courseId}
    public function destroy(string $courseId): JsonResponse
    {
        try {
            $result = $this->wishlistService->remove((int) $courseId);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }
}